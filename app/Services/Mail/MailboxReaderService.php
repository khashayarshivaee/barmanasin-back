<?php

namespace App\Services\Mail;

use Illuminate\Support\Facades\Process;
use JsonException;
use RuntimeException;

class MailboxReaderService
{
    private const READER = '/usr/local/bin/barmanasin-mail-reader';

    /**
     * @return array<int, array<string, mixed>>
     */
    public function inbox(string $mailboxAddress): array
    {
        $mailboxAddress = $this->normalizeMailboxAddress(
            $mailboxAddress,
        );

        $messages = $this->runReader([
            'inbox-list',
            $mailboxAddress,
        ]);

        $normalized = array_map(
            fn (array $message): array => $this->normalizeInboxMessage($message),
            $messages,
        );

        usort(
            $normalized,
            static fn (array $a, array $b): int =>
                (int) $b['uid'] <=> (int) $a['uid'],
        );

        return $normalized;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function message(
        string $mailboxAddress,
        string|int $uid,
    ): ?array {
        $mailboxAddress = $this->normalizeMailboxAddress(
            $mailboxAddress,
        );

        $uid = (string) $uid;

        if (! preg_match('/^[1-9][0-9]*$/', $uid)) {
            throw new RuntimeException(
                'Invalid message UID.',
            );
        }

        $messages = $this->runReader([
            'message-get',
            $mailboxAddress,
            $uid,
        ]);

        if ($messages === []) {
            return null;
        }

        $message = $messages[0] ?? null;

        if (! is_array($message)) {
            return null;
        }

        return $this->normalizeDetailedMessage(
            $message,
        );
    }

    /**
     * @param array<int, string> $arguments
     * @return array<int, array<string, mixed>>
     */
    private function runReader(array $arguments): array
    {
        $result = Process::timeout(10)->run([
            '/usr/bin/sudo',
            '-n',
            self::READER,
            ...$arguments,
        ]);

        if ($result->failed()) {
            throw new RuntimeException(
                'Unable to read mailbox.',
            );
        }

        try {
            $payload = json_decode(
                $result->output(),
                true,
                512,
                JSON_THROW_ON_ERROR,
            );
        } catch (JsonException $exception) {
            throw new RuntimeException(
                'Mailbox reader returned invalid JSON.',
                previous: $exception,
            );
        }

        if (! is_array($payload)) {
            throw new RuntimeException(
                'Mailbox reader returned an invalid response.',
            );
        }

        return array_values(
            array_filter(
                $payload,
                static fn (mixed $item): bool => is_array($item),
            ),
        );
    }

    private function normalizeMailboxAddress(
        string $mailboxAddress,
    ): string {
        $mailboxAddress = mb_strtolower(
            trim($mailboxAddress),
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

    /**
     * @param array<string, mixed> $message
     * @return array<string, mixed>
     */
    private function normalizeInboxMessage(
        array $message,
    ): array {
        $flags = $this->normalizeFlags(
            (string) ($message['flags'] ?? ''),
        );

        $from = $this->parseAddress(
            (string) ($message['hdr.from'] ?? ''),
        );

        return [
            'uid' => (string) ($message['uid'] ?? ''),

            'flags' => $flags,

            'unread' => ! in_array('\\Seen', $flags, true),

            'starred' => in_array('\\Flagged', $flags, true),

            'from' => $from,

            'to' => trim(
                (string) ($message['hdr.to'] ?? ''),
            ),

            'subject' => $this->normalizeHeader(
                (string) ($message['hdr.subject'] ?? ''),
                '(No subject)',
            ),

            'date' => trim(
                (string) ($message['hdr.date'] ?? ''),
            ),
        ];
    }

    /**
     * @param array<string, mixed> $message
     * @return array<string, mixed>
     */
    private function normalizeDetailedMessage(
        array $message,
    ): array {
        $flags = $this->normalizeFlags(
            (string) ($message['flags'] ?? ''),
        );

        return [
            'uid' => (string) ($message['uid'] ?? ''),

            'flags' => $flags,

            'unread' => ! in_array('\\Seen', $flags, true),

            'starred' => in_array('\\Flagged', $flags, true),

            'size' => (int) ($message['size.virtual'] ?? 0),

            'from' => $this->parseAddress(
                (string) ($message['hdr.from'] ?? ''),
            ),

            'to' => trim(
                (string) ($message['hdr.to'] ?? ''),
            ),

            'cc' => trim(
                (string) ($message['hdr.cc'] ?? ''),
            ),

            'reply_to' => trim(
                (string) ($message['hdr.reply-to'] ?? ''),
            ),

            'subject' => $this->normalizeHeader(
                (string) ($message['hdr.subject'] ?? ''),
                '(No subject)',
            ),

            'date' => trim(
                (string) ($message['hdr.date'] ?? ''),
            ),

            'message_id' => trim(
                (string) ($message['hdr.message-id'] ?? ''),
            ),

            'body_structure' => trim(
                (string) ($message['imap.bodystructure'] ?? ''),
            ),

            'body' => [
                'text' => $this->normalizeBody(
                    (string) ($message['body.1'] ?? ''),
                ),

                'html' => $this->normalizeBody(
                    (string) ($message['body.2'] ?? ''),
                ),
            ],
        ];
    }

    /**
     * @return array<int, string>
     */
    private function normalizeFlags(string $flags): array
    {
        $flags = trim($flags);

        if ($flags === '') {
            return [];
        }

        return array_values(
            array_filter(
                preg_split('/\s+/', $flags) ?: [],
            ),
        );
    }

    /**
     * @return array{
     *     name: string,
     *     address: string,
     *     raw: string
     * }
     */
    private function parseAddress(string $value): array
    {
        $value = trim($value);

        if (
            preg_match(
                '/^(?:"?(.+?)"?\s*)?<([^<>]+)>$/',
                $value,
                $matches,
            )
        ) {
            $address = trim($matches[2]);

            $name = trim(
                isset($matches[1])
                    ? trim($matches[1], "\" \t\n\r\0\x0B")
                    : '',
            );

            return [
                'name' => $name !== ''
                    ? $name
                    : $address,

                'address' => $address,

                'raw' => $value,
            ];
        }

        return [
            'name' => $value,

            'address' => filter_var(
                $value,
                FILTER_VALIDATE_EMAIL,
            )
                ? $value
                : '',

            'raw' => $value,
        ];
    }

    private function normalizeHeader(
        string $value,
        string $fallback,
    ): string {
        $value = trim($value);

        return $value !== ''
            ? $value
            : $fallback;
    }

    private function normalizeBody(
        string $value,
    ): string {
        return trim(
            str_replace("\r\n", "\n", $value),
        );
    }
}
