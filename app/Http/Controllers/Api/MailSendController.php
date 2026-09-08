<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Mail\MailSendService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class MailSendController extends Controller
{
    private const MAX_ATTACHMENTS = 5;

    private const MAX_ATTACHMENT_BYTES = 2 * 1024 * 1024;

    private const MAX_TOTAL_ATTACHMENT_BYTES = 6 * 1024 * 1024;

    public function __invoke(
        Request $request,
        MailSendService $mailSendService,
    ): JsonResponse {
        $user = $request->user();

        if (
            ! $user
            || ! $user->isMailUser()
            || ! $user->mailboxIsReady()
            || blank($user->mailbox_address)
        ) {
            return response()->json([
                'message' => 'This mail account is not available.',
            ], 403);
        }

        $validated = $request->validate([
            'to' => [
                'required',
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
                'file',
                'max:2048',
            ],
        ]);

        $attachments = $request->file(
            'attachments',
            [],
        );

        if (! is_array($attachments)) {
            $attachments = [];
        }

        $totalAttachmentBytes = 0;

        foreach ($attachments as $attachment) {
            if (! $attachment->isValid()) {
                throw ValidationException::withMessages([
                    'attachments' => [
                        'One or more attachments are invalid.',
                    ],
                ]);
            }

            $size = $attachment->getSize();

            if (
                ! is_int($size)
                || $size < 0
            ) {
                throw ValidationException::withMessages([
                    'attachments' => [
                        'Unable to determine attachment size.',
                    ],
                ]);
            }

            if ($size > self::MAX_ATTACHMENT_BYTES) {
                throw ValidationException::withMessages([
                    'attachments' => [
                        'Each attachment must be 2 MB or smaller.',
                    ],
                ]);
            }

            $totalAttachmentBytes += $size;
        }

        if (
            $totalAttachmentBytes
            > self::MAX_TOTAL_ATTACHMENT_BYTES
        ) {
            throw ValidationException::withMessages([
                'attachments' => [
                    'Attachments may not exceed 6 MB in total.',
                ],
            ]);
        }

        $to = $this->parseRecipients(
            (string) $validated['to'],
        );

        $cc = $this->parseRecipients(
            (string) ($validated['cc'] ?? ''),
        );

        $bcc = $this->parseRecipients(
            (string) ($validated['bcc'] ?? ''),
        );

        if ($to === []) {
            throw ValidationException::withMessages([
                'to' => [
                    'Add at least one recipient.',
                ],
            ]);
        }

        try {
            $result = $mailSendService->send(
                mailboxAddress: $user->mailbox_address,
                to: $to,
                cc: $cc,
                bcc: $bcc,
                subject: (string) ($validated['subject'] ?? ''),
                body: (string) ($validated['body'] ?? ''),
                attachments: $attachments,
            );
        } catch (RuntimeException $exception) {
            report($exception);

            return response()->json([
                'message' => 'Unable to send message.',
            ], 503);
        }

        return response()->json([
            'message' => $result['sent_saved']
                ? 'Message sent successfully.'
                : 'Message sent, but the Sent copy could not be saved.',

            'submitted' => $result['submitted'],
            'sent_saved' => $result['sent_saved'],
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
            $address = mb_strtolower(
                trim($part),
            );

            if ($address === '') {
                continue;
            }

            if (
                str_starts_with($address, '-')
                || filter_var(
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

            $recipients[$address] = $address;
        }

        return array_values(
            $recipients,
        );
    }
}
