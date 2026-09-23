<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TeamProfile;
use Illuminate\Http\JsonResponse;

class PublicTeamController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $members = TeamProfile::query()
            ->with('user')
            ->where('is_public', true)
            ->where(
                'approval_status',
                TeamProfile::STATUS_APPROVED,
            )
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(
                fn (TeamProfile $profile): array => [
                    'id' =>
                        $profile->id,

                    'slug' =>
                        $profile->slug,

                    'name' => [
                        'en' =>
                            $profile->name_en,

                        'fa' =>
                            $profile->name_fa,
                    ],

                    'job_title' => [
                        'en' =>
                            $profile->job_title_en,

                        'fa' =>
                            $profile->job_title_fa,
                    ],

                    'department' => [
                        'en' =>
                            $profile->department_en,

                        'fa' =>
                            $profile->department_fa,
                    ],

                    'bio' => [
                        'en' =>
                            $profile->bio_en,

                        'fa' =>
                            $profile->bio_fa,
                    ],

                    'photo_url' =>
                        $profile->photoUrl(),

                    'linkedin_url' =>
                        $profile->linkedin_url,

                    'email' =>
                        $profile->show_email
                            ? $profile->public_email
                            : null,

                    'phone' =>
                        $profile->show_phone
                            ? $profile->public_phone
                            : null,
                ]
            )
            ->values();

        return response()->json([
            'members' => $members,
        ]);
    }
}
