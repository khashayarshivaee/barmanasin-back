<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TeamProfile;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use RuntimeException;
use Throwable;

class MailTeamProfileController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $user = $this->mailUser($request);

        $profile = $user
            ->teamProfile()
            ->first();

        return response()->json([
            'profile' => $this->profilePayload(
                $user,
                $profile,
            ),
        ]);
    }


    public function update(Request $request): JsonResponse
    {
        $user = $this->mailUser($request);

        $wantsPublic =
            $request->boolean('is_public');

        $validated = $request->validate([
            'name_en' => [
                Rule::requiredIf($wantsPublic),
                'nullable',
                'string',
                'max:120',
            ],

            'name_fa' => [
                Rule::requiredIf($wantsPublic),
                'nullable',
                'string',
                'max:120',
            ],

            'job_title_en' => [
                Rule::requiredIf($wantsPublic),
                'nullable',
                'string',
                'max:160',
            ],

            'job_title_fa' => [
                Rule::requiredIf($wantsPublic),
                'nullable',
                'string',
                'max:160',
            ],

            'department_en' => [
                'nullable',
                'string',
                'max:160',
            ],

            'department_fa' => [
                'nullable',
                'string',
                'max:160',
            ],

            'bio_en' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'bio_fa' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'linkedin_url' => [
                'nullable',
                'url:http,https',
                'max:500',
            ],

            'public_email' => [
                Rule::requiredIf(
                    $request->boolean('show_email'),
                ),
                'nullable',
                'email',
                'max:255',
            ],

            'public_phone' => [
                Rule::requiredIf(
                    $request->boolean('show_phone'),
                ),
                'nullable',
                'string',
                'max:50',
            ],

            'show_email' => [
                'required',
                'boolean',
            ],

            'show_phone' => [
                'required',
                'boolean',
            ],

            'is_public' => [
                'required',
                'boolean',
            ],
        ]);


        $profile = DB::transaction(
            function () use (
                $user,
                $validated,
                $wantsPublic,
            ) {
                $profile = TeamProfile::query()
                    ->where('user_id', $user->id)
                    ->lockForUpdate()
                    ->first();

                if (! $profile) {
                    $profile = new TeamProfile();
                    $profile->user_id = $user->id;
                }


                $profile->fill([
                    'name_en' =>
                        $this->nullableString(
                            $validated['name_en'] ?? null,
                        ),

                    'name_fa' =>
                        $this->nullableString(
                            $validated['name_fa'] ?? null,
                        ),

                    'job_title_en' =>
                        $this->nullableString(
                            $validated['job_title_en'] ?? null,
                        ),

                    'job_title_fa' =>
                        $this->nullableString(
                            $validated['job_title_fa'] ?? null,
                        ),

                    'department_en' =>
                        $this->nullableString(
                            $validated['department_en'] ?? null,
                        ),

                    'department_fa' =>
                        $this->nullableString(
                            $validated['department_fa'] ?? null,
                        ),

                    'bio_en' =>
                        $this->nullableString(
                            $validated['bio_en'] ?? null,
                        ),

                    'bio_fa' =>
                        $this->nullableString(
                            $validated['bio_fa'] ?? null,
                        ),

                    'linkedin_url' =>
                        $this->nullableString(
                            $validated['linkedin_url'] ?? null,
                        ),

                    'public_email' =>
                        $this->nullableString(
                            $validated['public_email'] ?? null,
                        ),

                    'public_phone' =>
                        $this->nullableString(
                            $validated['public_phone'] ?? null,
                        ),

                    'show_email' =>
                        (bool) $validated['show_email'],

                    'show_phone' =>
                        (bool) $validated['show_phone'],

                    'is_public' =>
                        (bool) $validated['is_public'],
                ]);


                /*
                 * Any member-controlled change invalidates
                 * the previous administrative approval.
                 */
                $profile->approval_status =
                    $wantsPublic
                        ? TeamProfile::STATUS_PENDING
                        : TeamProfile::STATUS_DRAFT;

                $profile->submitted_at =
                    $wantsPublic
                        ? now()
                        : null;

                $profile->approved_at = null;
                $profile->approved_by = null;

                $profile->save();

                return $profile;
            },
        );


        return response()->json([
            'message' =>
                $wantsPublic
                    ? 'Team profile submitted for approval.'
                    : 'Team profile saved successfully.',

            'profile' => $this->profilePayload(
                $user,
                $profile,
            ),
        ]);
    }


    public function storePhoto(
        Request $request,
    ): JsonResponse {
        $user = $this->mailUser($request);

        $request->validate([
            'photo' => [
                'required',
                'file',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
                'dimensions:max_width=6000,max_height=6000',
            ],
        ]);


        $path = $request
            ->file('photo')
            ->store(
                'team-profiles/'.$user->id,
                'public',
            );


        if (! is_string($path) || $path === '') {
            throw new RuntimeException(
                'Unable to store the team profile photo.',
            );
        }


        try {
            [$profile, $oldPath] =
                DB::transaction(
                    function () use (
                        $user,
                        $path,
                    ) {
                        $profile = TeamProfile::query()
                            ->where(
                                'user_id',
                                $user->id,
                            )
                            ->lockForUpdate()
                            ->first();

                        if (! $profile) {
                            $profile = new TeamProfile();
                            $profile->user_id = $user->id;
                            $profile->approval_status =
                                TeamProfile::STATUS_DRAFT;
                        }


                        $oldPath =
                            $profile->photo_path;


                        $profile->photo_path =
                            $path;


                        /*
                         * Changing the public profile photo
                         * requires approval again.
                         */
                        if ($profile->is_public) {
                            $profile->approval_status =
                                TeamProfile::STATUS_PENDING;

                            $profile->submitted_at =
                                now();
                        } else {
                            $profile->approval_status =
                                TeamProfile::STATUS_DRAFT;

                            $profile->submitted_at =
                                null;
                        }


                        $profile->approved_at = null;
                        $profile->approved_by = null;

                        $profile->save();


                        return [
                            $profile,
                            $oldPath,
                        ];
                    },
                );

        } catch (Throwable $exception) {
            $this->deleteStoredPhoto(
                $user,
                $path,
            );

            throw $exception;
        }


        $this->deleteStoredPhoto(
            $user,
            $oldPath,
        );


        return response()->json([
            'message' =>
                'Team profile photo updated successfully.',

            'profile' => $this->profilePayload(
                $user,
                $profile,
            ),
        ]);
    }


    public function destroyPhoto(
        Request $request,
    ): JsonResponse {
        $user = $this->mailUser($request);


        [$profile, $oldPath] =
            DB::transaction(
                function () use ($user) {
                    $profile = TeamProfile::query()
                        ->where(
                            'user_id',
                            $user->id,
                        )
                        ->lockForUpdate()
                        ->first();


                    if (! $profile) {
                        return [
                            null,
                            null,
                        ];
                    }


                    $oldPath =
                        $profile->photo_path;

                    $profile->photo_path = null;


                    if ($profile->is_public) {
                        $profile->approval_status =
                            TeamProfile::STATUS_PENDING;

                        $profile->submitted_at =
                            now();
                    } else {
                        $profile->approval_status =
                            TeamProfile::STATUS_DRAFT;

                        $profile->submitted_at =
                            null;
                    }


                    $profile->approved_at = null;
                    $profile->approved_by = null;

                    $profile->save();


                    return [
                        $profile,
                        $oldPath,
                    ];
                },
            );


        $this->deleteStoredPhoto(
            $user,
            $oldPath,
        );


        return response()->json([
            'message' =>
                'Team profile photo removed successfully.',

            'profile' => $this->profilePayload(
                $user,
                $profile,
            ),
        ]);
    }


    private function mailUser(
        Request $request,
    ): User {
        $user = $request->user();

        abort_unless(
            $user instanceof User,
            401,
        );

        abort_unless(
            $user->isMailUser()
            && $user->mailboxIsReady(),
            403,
            'This mail account is not available.',
        );

        return $user;
    }


    private function profilePayload(
        User $user,
        ?TeamProfile $profile,
    ): array {
        return [
            'id' =>
                $profile?->id,

            'name_en' =>
                $profile?->name_en
                ?? $user->name,

            'name_fa' =>
                $profile?->name_fa,

            'job_title_en' =>
                $profile?->job_title_en,

            'job_title_fa' =>
                $profile?->job_title_fa,

            'department_en' =>
                $profile?->department_en,

            'department_fa' =>
                $profile?->department_fa,

            'bio_en' =>
                $profile?->bio_en,

            'bio_fa' =>
                $profile?->bio_fa,

            'photo_url' =>
                $profile?->photoUrl()
                ?? $user->avatarUrl(),

            'has_custom_photo' =>
                filled($profile?->photo_path),

            'linkedin_url' =>
                $profile?->linkedin_url,

            'public_email' =>
                $profile?->public_email
                ?? $user->mailbox_address,

            'public_phone' =>
                $profile?->public_phone,

            'show_email' =>
                $profile?->show_email
                ?? false,

            'show_phone' =>
                $profile?->show_phone
                ?? false,

            'is_public' =>
                $profile?->is_public
                ?? false,

            'approval_status' =>
                $profile?->approval_status
                ?? TeamProfile::STATUS_DRAFT,

            'submitted_at' =>
                $profile?->submitted_at
                    ?->toISOString(),

            'approved_at' =>
                $profile?->approved_at
                    ?->toISOString(),
        ];
    }


    private function nullableString(
        mixed $value,
    ): ?string {
        if (! is_string($value)) {
            return null;
        }

        $value = trim($value);

        return $value === ''
            ? null
            : $value;
    }


    private function deleteStoredPhoto(
        User $user,
        ?string $path,
    ): void {
        if (
            blank($path)
            || ! str_starts_with(
                $path,
                'team-profiles/'.$user->id.'/',
            )
        ) {
            return;
        }


        try {
            if (
                ! Storage::disk('public')
                    ->delete($path)
            ) {
                report(
                    new RuntimeException(
                        'Unable to remove an unused team profile photo.',
                    ),
                );
            }
        } catch (Throwable $exception) {
            report($exception);
        }
    }
}
