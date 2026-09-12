<?php

namespace App\Services\Mail;

use App\Models\MailAttachment;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Symfony\Component\Mime\Email;

class MailDraftService
{
    public function __construct(
        private readonly MailDraftStorageService $draftStorage,
    ) {
    }


    /**
     * @param array<int, string> $to
     * @param array<int, string> $cc
     * @param array<int, string> $bcc
     * @param iterable<int, MailAttachment> $attachments
     * @param array<int, array{
     *     filename?: string,
     *     content_type?: string,
     *     content?: string
     * }> $existingAttachments
     */
    public function save(
        string $mailboxAddress,
        array $to = [],
        array $cc = [],
        array $bcc = [],
        string $subject = '',
        string $body = '',
        iterable $attachments = [],
        array $existingAttachments = [],
    ): void {
        $mailboxAddress =
            $this->normalizeMailboxAddress(
                $mailboxAddress,
            );

        $to =
            $this->normalizeRecipients(
                $to,
            );

        $cc =
            $this->normalizeRecipients(
                $cc,
            );

        $bcc =
            $this->normalizeRecipients(
                $bcc,
            );


        $email = new Email();


        $email->from(
            $mailboxAddress,
        );


        if ($to !== []) {
            $email->to(
                ...$to,
            );
        }


        if ($cc !== []) {
            $email->cc(
                ...$cc,
            );
        }


        if ($bcc !== []) {
            $email->bcc(
                ...$bcc,
            );
        }


        $email->subject(
            trim($subject),
        );


        $email->text(
            $body,
            'utf-8',
        );


        /*
         * Newly uploaded attachments.
         */
        foreach ($attachments as $attachment) {
            $this->attachUploadedFile(
                $email,
                $attachment,
            );
        }


        /*
         * Attachments already stored inside
         * the existing Dovecot draft.
         */
        foreach (
            $existingAttachments
            as $attachment
        ) {
            $this->attachExistingFile(
                $email,
                $attachment,
            );
        }


        $rawMessage =
            $email->toString();


        if (trim($rawMessage) === '') {
            throw new RuntimeException(
                'Unable to generate draft MIME.',
            );
        }


        $this->draftStorage->save(
            $mailboxAddress,
            $rawMessage,
        );
    }


    private function attachUploadedFile(
        Email $email,
        MailAttachment $attachment,
    ): void {
        $path =
            Storage::disk(
                $attachment->disk,
            )->path(
                $attachment->path,
            );


        if (! is_file($path)) {
            throw new RuntimeException(
                'Draft attachment file is not available.',
            );
        }


        $filename =
            $this->normalizeAttachmentName(
                $attachment->original_name,
            );


        $mimeType =
            trim(
                (string) $attachment->mime_type,
            );


        if ($mimeType === '') {
            $mimeType =
                'application/octet-stream';
        }


        $email->attachFromPath(
            $path,
            $filename,
            $mimeType,
        );
    }


    /**
     * @param array{
     *     filename?: string,
     *     content_type?: string,
     *     content?: string
     * } $attachment
     */
    private function attachExistingFile(
        Email $email,
        array $attachment,
    ): void {
        $content =
            (string) (
                $attachment['content'] ?? ''
            );


        if ($content === '') {
            throw new RuntimeException(
                'Existing draft attachment content is empty.',
            );
        }


        $filename =
            $this->normalizeAttachmentName(
                (string) (
                    $attachment['filename']
                    ?? 'attachment'
                ),
            );


        $mimeType =
            trim(
                (string) (
                    $attachment['content_type']
                    ?? 'application/octet-stream'
                ),
            );


        if ($mimeType === '') {
            $mimeType =
                'application/octet-stream';
        }


        $email->attach(
            $content,
            $filename,
            $mimeType,
        );
    }


    /**
     * @param array<int, string> $recipients
     * @return array<int, string>
     */
    private function normalizeRecipients(
        array $recipients,
    ): array {
        $normalized = [];


        foreach ($recipients as $recipient) {
            $recipient =
                mb_strtolower(
                    trim(
                        (string) $recipient,
                    ),
                );


            if ($recipient === '') {
                continue;
            }


            if (
                str_starts_with(
                    $recipient,
                    '-',
                )
                ||
                filter_var(
                    $recipient,
                    FILTER_VALIDATE_EMAIL,
                ) === false
            ) {
                throw new RuntimeException(
                    'Invalid draft recipient email address.',
                );
            }


            $normalized[$recipient] =
                $recipient;
        }


        return array_values(
            $normalized,
        );
    }


    private function normalizeMailboxAddress(
        string $mailboxAddress,
    ): string {
        $mailboxAddress =
            mb_strtolower(
                trim(
                    $mailboxAddress,
                ),
            );


        if (
            ! preg_match(
                '/^[a-z0-9._%+\-]+@barmanasin\.com$/',
                $mailboxAddress,
            )
        ) {
            throw new RuntimeException(
                'Invalid Barmanasin mailbox address.',
            );
        }


        return $mailboxAddress;
    }


    private function normalizeAttachmentName(
        string $filename,
    ): string {
        $filename =
            basename(
                trim(
                    $filename,
                ),
            );


        $filename =
            preg_replace(
                '/[\x00-\x1F\x7F]/u',
                '',
                $filename,
            ) ?? '';


        $filename =
            trim(
                $filename,
            );


        return $filename !== ''
            ? $filename
            : 'attachment';
    }
}
