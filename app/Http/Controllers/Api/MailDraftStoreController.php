<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MailAttachment;
use App\Services\Mail\MailDraftService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class MailDraftStoreController extends Controller
{
    private const MAX_ATTACHMENTS = 5;

    public function __invoke(
        Request $request,
        MailDraftService $mailDraftService,
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

            'attachments' => [
                'sometimes',
                'array',
                'max:' . self::MAX_ATTACHMENTS,
            ],

            'attachments.*' => [
                'integer',
            ],
        ]);


        $attachmentIds =
            $validated['attachments'] ?? [];


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


        $to = $this->parseRecipients(
            (string) (
                $validated['to'] ?? ''
            ),
        );

        $cc = $this->parseRecipients(
            (string) (
                $validated['cc'] ?? ''
            ),
        );

        $bcc = $this->parseRecipients(
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
            || $attachments->isNotEmpty();


        if (! $hasContent) {
            throw ValidationException::withMessages([
                'draft' => [
                    'Cannot save an empty draft.',
                ],
            ]);
        }


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
            );
        } catch (RuntimeException $exception) {
            report($exception);

            return response()->json([
                'message' =>
                    'Unable to save draft.',
            ], 503);
        }


        foreach ($attachments as $attachment) {
            $attachment->update([
                'is_used' => true,
                'used_at' => now(),
            ]);
        }


        return response()->json([
            'message' =>
                'Draft saved successfully.',

            'saved' =>
                true,
        ]);
    }


    /**
     * @return array<int, string>
     */
    private function parseRecipients(
        string $value,
    ): array {
        $parts = preg_split(
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
