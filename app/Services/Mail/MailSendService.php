<?php

namespace App\Services\Mail;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Process;
use RuntimeException;
use Symfony\Component\Mime\Email;

class MailSendService
{
    private const SENDMAIL = '/usr/sbin/sendmail';

    public function __construct(
        private readonly MailSentStorageService $sentStorage,
    ) {
    }

    /**
     * @param array<int, string> $to
     * @param array<int, string> $cc
     * @param array<int, string> $bcc
     * @param array<int, UploadedFile> $attachments
     *
     * @return array{
     *     submitted: bool,
     *     sent_saved: bool
     * }
     */
    public function send(
        string $mailboxAddress,
        array $to,
        array $cc = [],
        array $bcc = [],
        string $subject = '',
        string $body = '',
        array $attachments = [],
    ): array {
        $mailboxAddress =
            $this->normalizeMailboxAddress(
                $mailboxAddress,
            );

        $to = $this->normalizeRecipients(
            $to,
        );

        $cc = $this->normalizeRecipients(
            $cc,
        );

        $bcc = $this->normalizeRecipients(
            $bcc,
        );

        if ($to === []) {
            throw new RuntimeException(
                'At least one recipient is required.',
            );
        }

        $email = new Email();

        $email->from(
            $mailboxAddress,
        );

        $email->to(
            ...$to,
        );

        if ($cc !== []) {
            $email->cc(
                ...$cc,
            );
        }

        /*
         * Intentionally do NOT add Bcc to the
         * MIME headers.
         *
         * Bcc recipients are added only to the
         * Postfix envelope below.
         */
        $email->subject(
            trim($subject),
        );

        $email->text(
            $body,
            'utf-8',
        );

        foreach ($attachments as $attachment) {
            $this->attachFile(
                $email,
                $attachment,
            );
        }

        /*
         * Symfony generates the MIME structure,
         * boundaries, transfer encodings, Date,
         * Message-ID, etc.
         */
        $rawMessage =
            $email->toString();

        if (trim($rawMessage) === '') {
            throw new RuntimeException(
                'Unable to generate message MIME.',
            );
        }

        /*
         * Envelope recipients include To + Cc + Bcc.
         *
         * Bcc is therefore delivered by Postfix,
         * while remaining absent from visible
         * message headers.
         */
        $envelopeRecipients =
            array_values(
                array_unique([
                    ...$to,
                    ...$cc,
                    ...$bcc,
                ]),
            );

        $command = [
            self::SENDMAIL,
            '-i',
            '-f',
            $mailboxAddress,
            ...$envelopeRecipients,
        ];

        $result = Process::timeout(60)
            ->input($rawMessage)
            ->run($command);

        if ($result->failed()) {
            $error = trim(
                $result->errorOutput(),
            );

            throw new RuntimeException(
                $error !== ''
                    ? 'Postfix rejected the message: ' . $error
                    : 'Postfix rejected the message.',
            );
        }

        /*
         * At this point Postfix has accepted the
         * message for delivery.
         *
         * A Sent-storage failure must NOT turn the
         * operation into an apparent send failure,
         * otherwise the user could resend the same
         * message and create duplicates.
         */
        $sentSaved = true;

        try {
            $this->sentStorage->save(
                $mailboxAddress,
                $rawMessage,
            );
        } catch (RuntimeException $exception) {
            $sentSaved = false;

            report(
                $exception,
            );
        }

        return [
            'submitted' => true,
            'sent_saved' => $sentSaved,
        ];
    }

    private function attachFile(
        Email $email,
        UploadedFile $attachment,
    ): void {
        if (! $attachment->isValid()) {
            throw new RuntimeException(
                'Invalid mail attachment.',
            );
        }

        $path =
            $attachment->getPathname();

        if (
            $path === ''
            || ! is_file($path)
        ) {
            throw new RuntimeException(
                'Attachment file is not available.',
            );
        }

        $filename =
            $this->normalizeAttachmentName(
                $attachment->getClientOriginalName(),
            );

        $mimeType =
            $attachment->getMimeType()
                ?: 'application/octet-stream';

        $email->attachFromPath(
            $path,
            $filename,
            $mimeType,
        );
    }

    /**
     * @param array<int, string> $recipients
     *
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

            /*
             * Recipient arguments are passed to
             * sendmail directly, so values beginning
             * with "-" are explicitly forbidden.
             */
            if (
                str_starts_with(
                    $recipient,
                    '-',
                )
                || filter_var(
                    $recipient,
                    FILTER_VALIDATE_EMAIL,
                ) === false
            ) {
                throw new RuntimeException(
                    'Invalid recipient email address.',
                );
            }

            $normalized[
            $recipient
            ] = $recipient;
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

        if (! preg_match(
            '/^[a-z0-9._%+\-]+@barmanasin\.com$/',
            $mailboxAddress,
        )) {
            throw new RuntimeException(
                'Invalid Barmanasin mailbox address.',
            );
        }

        return $mailboxAddress;
    }

    private function normalizeAttachmentName(
        string $filename,
    ): string {
        $filename = basename(
            trim($filename),
        );

        $filename = preg_replace(
            '/[\x00-\x1F\x7F]/u',
            '',
            $filename,
        ) ?? '';

        $filename = trim(
            $filename,
        );

        if ($filename === '') {
            return 'attachment';
        }

        return $filename;
    }
}
