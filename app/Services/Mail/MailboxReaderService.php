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
        $mailboxAddress = mb_strtolower(trim($mailboxAddress));

        if (! preg_match(
            '/^[a-z0-9._%+\-]+@barmanasin\.com$/',
            $mailboxAddress,
        )) {
            throw new RuntimeException(
                'Invalid Barmanasin mailbox address.',
            );
        }

        $result = Process::timeout(10)->run([
            '/usr/bin/sudo',
            '-n',
            self::READER,
            'inbox-list',
            $mailboxAddress,
        ]);

        if ($result->failed()) {
            throw new RuntimeException(
                'Unable to read mailbox.',
            );
        }

        try {
            $messages = json_decode(
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

        if (! is_array($messages)) {
            throw new RuntimeException(
                'Mailbox reader returned an invalid response.',
            );
        }

        $normalized = array_map(
            fn (array $message): array => $this->normalizeMessage($message),
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
     * @param array<string, mixed> $message
     * @return array<string, mixed>
     */
    private function normalizeMessage(array $message): array
    {
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

            'from' => [
                'name' => $from['name'],
                'address' => $from['address'],
                'raw' => $from['raw'],
            ],

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
}
