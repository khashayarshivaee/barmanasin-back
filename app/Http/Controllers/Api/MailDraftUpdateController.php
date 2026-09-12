<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MailAttachment;
use App\Services\Mail\MailDraftService;
use App\Services\Mail\MailboxReaderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class MailDraftUpdateController extends Controller
{
    private const MAX_ATTACHMENTS = 5;

    public function __invoke(
        Request $request,
        string $uid,
        MailDraftService $mailDraftService,
        MailboxReaderService $mailboxReader,
    ): JsonResponse {
        $user = $request->user();

        if (
            ! $user
            || ! $user->isMailUser()
            || ! $user->mailboxIsReady()
            || blank($user->mailbox_address)
        ) {
            return response()->json([
                'message' =>
                    'This mail account is not available.',
            ], 403);
        }


        $validated = $request->validate([
            'to' => [
                'nullable',
                'string',
                'max:4000',
            ],

            'cc' => [
                'nullable',
                'string',
                'max:4000',
            ],

            'bcc' => [
                'nullable',
                'string',
                'max:4000',
            ],

            'subject' => [
                'nullable',
                'string',
                'max:998',
            ],

            'body' => [
                'nullable',
                'string',
                'max:1000000',
            ],

            /*
             * Newly uploaded attachments.
             */
            'attachments' => [
                'sometimes',
                'array',
                'max:' . self::MAX_ATTACHMENTS,
            ],

            'attachments.*' => [
                'integer',
            ],

            /*
             * Existing Dovecot attachment parts
             * that should remain in the draft.
             *
             * If omitted, all existing attachments
             * are preserved.
             */
            'existing_attachments' => [
                'sometimes',
                'array',
                'max:' . self::MAX_ATTACHMENTS,
            ],

            'existing_attachments.*' => [
                'string',
                'regex:/^[1-9][0-9]*(?:\.[1-9][0-9]*)*$/',
            ],
        ]);


        /*
         * Load the current draft before creating
         * its replacement.
         */
        try {
            $existingDraft =
                $mailboxReader->message(
                    $user->mailbox_address,
                    $uid,
                    'Drafts',
                );
        } catch (RuntimeException $exception) {
            report($exception);

            return response()->json([
                'message' =>
                    'Unable to load draft.',
            ], 503);
        }


        if ($existingDraft === null) {
            return response()->json([
                'message' =>
                    'Draft not found.',
            ], 404);
        }


        $existingMetadata =
            array_values(
                array_filter(
                    $existingDraft['attachments'] ?? [],
                    static fn (mixed $attachment): bool =>
                    is_array($attachment),
                ),
            );


        /*
         * If Front does not explicitly send a list,
         * preserve every existing attachment.
         */
        $keepParts =
            array_key_exists(
                'existing_attachments',
                $validated,
            )
                ? array_values(
                array_unique(
                    array_map(
                        'strval',
                        $validated['existing_attachments'],
                    ),
                ),
            )
                : array_values(
                array_filter(
                    array_map(
                        static fn (array $attachment): string =>
                        (string) (
                            $attachment['part'] ?? ''
                        ),
                        $existingMetadata,
                    ),
                ),
            );


        /*
         * Validate that requested existing parts
         * really belong to this draft.
         */
        $availableParts =
            array_values(
                array_filter(
                    array_map(
                        static fn (array $attachment): string =>
                        (string) (
                            $attachment['part'] ?? ''
                        ),
                        $existingMetadata,
                    ),
                ),
            );


        foreach ($keepParts as $part) {
            if (
                ! in_array(
                    $part,
                    $availableParts,
                    true,
                )
            ) {
                throw ValidationException::withMessages([
                    'existing_attachments' => [
                        'One or more existing attachments are invalid.',
                    ],
                ]);
            }
        }


        /*
         * Read existing attachment bytes from
         * the old Draft before deleting it.
         */
        $existingAttachments = [];


        try {
            foreach ($keepParts as $part) {
                $existingAttachments[] =
                    $mailboxReader->messageAttachment(
                        $user->mailbox_address,
                        $uid,
                        $part,
                        'Drafts',
                    );
            }
        } catch (RuntimeException $exception) {
            report($exception);

            return response()->json([
                'message' =>
                    'Unable to load existing draft attachments.',
            ], 503);
        }


        /*
         * Resolve newly uploaded files.
         */
        $attachmentIds =
            array_values(
                array_unique(
                    array_map(
                        'intval',
                        $validated['attachments'] ?? [],
                    ),
                ),
            );


        $attachments =
            MailAttachment::query()
                ->whereIn(
                    'id',
                    $attachmentIds,
                )
                ->where(
                    'user_id',
                    $user->id,
                )
                ->where(
                    'is_used',
                    false,
                )
                ->get();


        if (
            count($attachmentIds)
            !== $attachments->count()
        ) {
            throw ValidationException::withMessages([
                'attachments' => [
                    'One or more attachments are invalid.',
                ],
            ]);
        }


        if (
            count($keepParts)
            + $attachments->count()
            > self::MAX_ATTACHMENTS
        ) {
            throw ValidationException::withMessages([
                'attachments' => [
                    'A draft can contain at most 5 attachments.',
                ],
            ]);
        }


        $to =
            $this->parseRecipients(
                (string) (
                    $validated['to'] ?? ''
                ),
            );

        $cc =
            $this->parseRecipients(
                (string) (
                    $validated['cc'] ?? ''
                ),
            );

        $bcc =
            $this->parseRecipients(
                (string) (
                    $validated['bcc'] ?? ''
                ),
            );


        $hasContent =
            $to !== []
            || $cc !== []
            || $bcc !== []
            || trim(
                (string) (
                    $validated['subject'] ?? ''
                ),
            ) !== ''
            || trim(
                (string) (
                    $validated['body'] ?? ''
                ),
            ) !== ''
            || $attachments->isNotEmpty()
            || $existingAttachments !== [];


        if (! $hasContent) {
            throw ValidationException::withMessages([
                'draft' => [
                    'Cannot save an empty draft.',
                ],
            ]);
        }


        /*
         * Important order:
         *
         * 1. Save replacement first.
         * 2. Only then delete old Draft.
         *
         * This prevents data loss if the save fails.
         */
        try {
            $mailDraftService->save(
                mailboxAddress:
                $user->mailbox_address,

                to:
                $to,

                cc:
                $cc,

                bcc:
                $bcc,

                subject:
                (string) (
                    $validated['subject'] ?? ''
                ),

                body:
                (string) (
                    $validated['body'] ?? ''
                ),

                attachments:
                $attachments,

                existingAttachments:
                $existingAttachments,
            );


            $mailboxReader->deleteDraft(
                $user->mailbox_address,
                $uid,
            );
        } catch (RuntimeException $exception) {
            report($exception);

            return response()->json([
                'message' =>
                    'Unable to update draft.',
            ], 503);
        }


        /*
         * Uploaded temporary files have now been
         * embedded inside the stored Draft.
         */
        foreach ($attachments as $attachment) {
            $attachment->update([
                'is_used' => true,
                'used_at' => now(),
            ]);
        }


        return response()->json([
            'message' =>
                'Draft updated successfully.',

            'updated' =>
                true,
        ]);
    }


    /**
     * @return array<int, string>
     */
    private function parseRecipients(
        string $value,
    ): array {
        $parts =
            preg_split(
                '/[;,]+/',
                $value,
            );


        if ($parts === false) {
            return [];
        }


        $recipients = [];


        foreach ($parts as $part) {
            $address =
                mb_strtolower(
                    trim(
                        $part,
                    ),
                );


            if ($address === '') {
                continue;
            }


            if (
                str_starts_with(
                    $address,
                    '-',
                )
                ||
                filter_var(
                    $address,
                    FILTER_VALIDATE_EMAIL,
                ) === false
            ) {
                throw ValidationException::withMessages([
                    'recipients' => [
                        sprintf(
                            'Invalid email address: %s',
                            $address,
                        ),
                    ],
                ]);
            }


            $recipients[$address] =
                $address;
        }


        return array_values(
            $recipients,
        );
    }
}
