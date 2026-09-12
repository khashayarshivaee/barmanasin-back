<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MailAttachment;
use App\Services\Mail\MailSendService;
use App\Services\Mail\MailboxReaderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class MailSendController extends Controller
{
    private const MAX_ATTACHMENTS = 5;


    public function __invoke(
        Request $request,
        MailSendService $mailSendService,
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
             * Existing Draft being edited.
             */
            'draft_uid' => [
                'nullable',
                'integer',
                'min:1',
            ],


            /*
             * Existing MIME parts that should
             * remain when sending the Draft.
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


            /*
             * Lets Front explicitly say that
             * existing_attachments is intentional.
             *
             * Important when the user removes
             * every existing Draft attachment.
             */
            'existing_attachments_provided' => [
                'sometimes',
                'boolean',
            ],

        ]);


        $draftUid =
            isset($validated['draft_uid'])
                ? (string) (
            (int) $validated['draft_uid']
            )
                : null;


        /*
         * --------------------------------------------------
         * LOAD EXISTING DRAFT ATTACHMENTS
         * --------------------------------------------------
         */

        $existingAttachments = [];


        if ($draftUid !== null) {

            try {

                $draft =
                    $mailboxReader->message(
                        $user->mailbox_address,
                        $draftUid,
                        'Drafts',
                    );

            } catch (RuntimeException $exception) {

                report($exception);


                return response()->json([
                    'message' =>
                        'Unable to load draft.',
                ], 503);

            }


            if ($draft === null) {

                return response()->json([
                    'message' =>
                        'Draft not found.',
                ], 404);

            }


            $existingMetadata =
                array_values(
                    array_filter(
                        $draft['attachments'] ?? [],
                        static fn (mixed $attachment): bool =>
                        is_array($attachment),
                    ),
                );


            $availableParts =
                array_values(
                    array_filter(
                        array_map(
                            static fn (array $attachment): string =>
                            (string) (
                                $attachment['part']
                                ?? ''
                            ),
                            $existingMetadata,
                        ),
                    ),
                );


            /*
             * If Front explicitly supplied the
             * attachment state, use exactly that.
             *
             * This allows [] to mean:
             * remove all old attachments.
             */
            if (
                $request->boolean(
                    'existing_attachments_provided',
                )
            ) {

                $keepParts =
                    array_values(
                        array_unique(
                            array_map(
                                'strval',
                                $validated[
                                'existing_attachments'
                                ] ?? [],
                            ),
                        ),
                    );

            } else {

                /*
                 * Safe fallback:
                 * preserve all Draft attachments.
                 */
                $keepParts =
                    $availableParts;

            }


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


            try {

                foreach ($keepParts as $part) {

                    $existingAttachments[] =
                        $mailboxReader->messageAttachment(
                            $user->mailbox_address,
                            $draftUid,
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

        } elseif (
            array_key_exists(
                'existing_attachments',
                $validated,
            )
            ||
            $request->boolean(
                'existing_attachments_provided',
            )
        ) {

            throw ValidationException::withMessages([
                'draft_uid' => [
                    'A Draft UID is required for existing attachments.',
                ],
            ]);

        }


        /*
         * --------------------------------------------------
         * NEWLY UPLOADED ATTACHMENTS
         * --------------------------------------------------
         */

        $attachmentIds =
            array_values(
                array_unique(
                    array_map(
                        'intval',
                        $validated['attachments']
                        ?? [],
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
            count($existingAttachments)
            + $attachments->count()
            > self::MAX_ATTACHMENTS
        ) {
            throw ValidationException::withMessages([
                'attachments' => [
                    'A message can contain at most 5 attachments.',
                ],
            ]);
        }


        /*
         * --------------------------------------------------
         * RECIPIENTS
         * --------------------------------------------------
         */

        $to =
            $this->parseRecipients(
                (string) $validated['to'],
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


        if ($to === []) {

            throw ValidationException::withMessages([
                'to' => [
                    'Add at least one recipient.',
                ],
            ]);

        }


        /*
         * --------------------------------------------------
         * SEND
         * --------------------------------------------------
         */

        try {

            $result =
                $mailSendService->send(
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
                        $validated['subject']
                        ?? ''
                    ),

                    body:
                    (string) (
                        $validated['body']
                        ?? ''
                    ),

                    attachments:
                    $attachments,

                    existingAttachments:
                    $existingAttachments,
                );

        } catch (RuntimeException $exception) {

            report($exception);


            return response()->json([
                'message' =>
                    'Unable to send message.',
            ], 503);

        }


        /*
         * Message was successfully submitted.
         * Newly uploaded files can now be marked used.
         */
        foreach ($attachments as $attachment) {

            $attachment->update([
                'is_used' => true,
                'used_at' => now(),
            ]);

        }


        /*
         * --------------------------------------------------
         * DELETE SOURCE DRAFT
         * --------------------------------------------------
         *
         * Important:
         * We only do this AFTER successful send.
         *
         * If deletion fails, the email has still
         * been sent, so we must not return a fake
         * send failure.
         */

        $draftDeleted = null;


        if ($draftUid !== null) {

            try {

                $mailboxReader->deleteDraft(
                    $user->mailbox_address,
                    $draftUid,
                );


                $draftDeleted = true;

            } catch (RuntimeException $exception) {

                $draftDeleted = false;

                report($exception);

            }

        }


        /*
         * --------------------------------------------------
         * RESPONSE
         * --------------------------------------------------
         */

        if (
            $draftUid !== null
            && $draftDeleted === false
        ) {

            $message =
                'Message sent successfully, but the Draft could not be removed.';

        } elseif (! $result['sent_saved']) {

            $message =
                'Message sent, but the Sent copy could not be saved.';

        } else {

            $message =
                'Message sent successfully.';

        }


        return response()->json([

            'message' =>
                $message,

            'submitted' =>
                $result['submitted'],

            'sent_saved' =>
                $result['sent_saved'],

            'draft_deleted' =>
                $draftDeleted,

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
