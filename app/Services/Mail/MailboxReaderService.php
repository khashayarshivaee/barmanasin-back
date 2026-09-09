<?php

namespace App\Services\Mail;

use Illuminate\Support\Facades\Process;
use JsonException;
use RuntimeException;

class MailboxReaderService
{
    private const READER = '/usr/local/bin/barmanasin-mail-reader';

    private const ALLOWED_FOLDERS = [
        'INBOX',
        'Archive',
        'Trash',
        'Sent',
        'Drafts',
    ];

    /**
     * @return array<int, array<string, mixed>>
     */
    public function inbox(string $mailboxAddress): array
    {
        return $this->listMessages(
            $mailboxAddress,
            'inbox-list',
        );
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function sent(string $mailboxAddress): array
    {
        return $this->listMessages(
            $mailboxAddress,
            'sent-list',
        );
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function starred(string $mailboxAddress): array
    {
        return $this->listMessages(
            $mailboxAddress,
            'starred-list',
            true,
        );
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function archive(string $mailboxAddress): array
    {
        return $this->listMessages(
            $mailboxAddress,
            'archive-list',
        );
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function trash(string $mailboxAddress): array
    {
        return $this->listMessages(
            $mailboxAddress,
            'trash-list',
        );
    }

    /**
     * @return array<string, mixed>|null
     */
    public function message(
        string $mailboxAddress,
        string|int $uid,
        string $folder = 'INBOX',
    ): ?array {
        $mailboxAddress = $this->normalizeMailboxAddress(
            $mailboxAddress,
        );

        $folder = $this->normalizeFolder($folder);
        $uid = $this->normalizeMessageUid($uid);

        $messages = $this->runReaderJson([
            'message-get',
            $mailboxAddress,
            $folder,
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
     * @return array<string, mixed>
     */
    public function markSeen(
        string $mailboxAddress,
        string|int $uid,
        string $folder = 'INBOX',
    ): array {
        return $this->updateSeenState(
            $mailboxAddress,
            $uid,
            $folder,
            true,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function markUnseen(
        string $mailboxAddress,
        string|int $uid,
        string $folder = 'INBOX',
    ): array {
        return $this->updateSeenState(
            $mailboxAddress,
            $uid,
            $folder,
            false,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function markStarred(
        string $mailboxAddress,
        string|int $uid,
        string $folder = 'INBOX',
    ): array {
        return $this->updateStarredState(
            $mailboxAddress,
            $uid,
            $folder,
            true,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function markUnstarred(
        string $mailboxAddress,
        string|int $uid,
        string $folder = 'INBOX',
    ): array {
        return $this->updateStarredState(
            $mailboxAddress,
            $uid,
            $folder,
            false,
        );
    }

    public function archiveMessage(
        string $mailboxAddress,
        string|int $uid,
        string $folder = 'INBOX',
    ): void {
        $mailboxAddress = $this->normalizeMailboxAddress(
            $mailboxAddress,
        );

        $folder = $this->normalizeFolder($folder);
        $uid = $this->normalizeMessageUid($uid);

        if ($folder === 'Archive') {
            throw new RuntimeException(
                'Message is already archived.',
            );
        }

        $this->runReaderAction([
            'message-archive',
            $mailboxAddress,
            $folder,
            $uid,
        ]);
    }

    public function trashMessage(
        string $mailboxAddress,
        string|int $uid,
        string $folder = 'INBOX',
    ): void {
        $mailboxAddress = $this->normalizeMailboxAddress(
            $mailboxAddress,
        );

        $folder = $this->normalizeFolder($folder);
        $uid = $this->normalizeMessageUid($uid);

        if ($folder === 'Trash') {
            throw new RuntimeException(
                'Message is already in Trash.',
            );
        }

        $this->runReaderAction([
            'message-trash',
            $mailboxAddress,
            $folder,
            $uid,
        ]);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function listMessages(
        string $mailboxAddress,
        string $action,
        bool $sortByDate = false,
    ): array {
        $mailboxAddress = $this->normalizeMailboxAddress(
            $mailboxAddress,
        );

        $messages = $this->runReaderJson([
            $action,
            $mailboxAddress,
        ]);

        $normalized = array_map(
            fn (array $message): array =>
            $this->normalizeSummaryMessage($message),
            $messages,
        );

        if ($sortByDate) {
            usort(
                $normalized,
                static function (array $a, array $b): int {
                    $aTime = strtotime(
                        (string) ($a['date'] ?? ''),
                    ) ?: 0;

                    $bTime = strtotime(
                        (string) ($b['date'] ?? ''),
                    ) ?: 0;

                    return $bTime <=> $aTime;
                },
            );

            return $normalized;
        }

        usort(
            $normalized,
            static fn (array $a, array $b): int =>
                (int) $b['uid'] <=> (int) $a['uid'],
        );

        return $normalized;
    }

    /**
     * @return array<string, mixed>
     */
    private function updateSeenState(
        string $mailboxAddress,
        string|int $uid,
        string $folder,
        bool $seen,
    ): array {
        $mailboxAddress = $this->normalizeMailboxAddress(
            $mailboxAddress,
        );

        $folder = $this->normalizeFolder($folder);
        $uid = $this->normalizeMessageUid($uid);

        $this->runReaderAction([
            $seen
                ? 'message-seen'
                : 'message-unseen',
            $mailboxAddress,
            $folder,
            $uid,
        ]);

        return $this->reloadMessageAfterUpdate(
            $mailboxAddress,
            $uid,
            $folder,
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function updateStarredState(
        string $mailboxAddress,
        string|int $uid,
        string $folder,
        bool $starred,
    ): array {
        $mailboxAddress = $this->normalizeMailboxAddress(
            $mailboxAddress,
        );

        $folder = $this->normalizeFolder($folder);
        $uid = $this->normalizeMessageUid($uid);

        $this->runReaderAction([
            $starred
                ? 'message-star'
                : 'message-unstar',
            $mailboxAddress,
            $folder,
            $uid,
        ]);

        return $this->reloadMessageAfterUpdate(
            $mailboxAddress,
            $uid,
            $folder,
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function reloadMessageAfterUpdate(
        string $mailboxAddress,
        string $uid,
        string $folder,
    ): array {
        $message = $this->message(
            $mailboxAddress,
            $uid,
            $folder,
        );

        if ($message === null) {
            throw new RuntimeException(
                'Message not found after mailbox update.',
            );
        }

        return $message;
    }

    /**
     * @param array<int, string> $arguments
     * @return array<int, array<string, mixed>>
     */
    private function runReaderJson(
        array $arguments,
    ): array {
        $result = $this->runReader(
            $arguments,
        );

        try {
            $payload = json_decode(
                $result,
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
                static fn (mixed $item): bool =>
                is_array($item),
            ),
        );
    }

    /**
     * @param array<int, string> $arguments
     */
    private function runReaderAction(
        array $arguments,
    ): void {
        $this->runReader(
            $arguments,
        );
    }

    /**
     * @param array<int, string> $arguments
     */
    private function runReader(
        array $arguments,
    ): string {
        $result = Process::timeout(10)->run([
            '/usr/bin/sudo',
            '-n',
            self::READER,
            ...$arguments,
        ]);

        if ($result->failed()) {
            throw new RuntimeException(
                'Unable to read or update mailbox.',
            );
        }

        return $result->output();
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

    private function normalizeFolder(
        string $folder,
    ): string {
        $folder = trim($folder);

        if (! in_array(
            $folder,
            self::ALLOWED_FOLDERS,
            true,
        )) {
            throw new RuntimeException(
                'Invalid mailbox folder.',
            );
        }

        return $folder;
    }

    private function normalizeMessageUid(
        string|int $uid,
    ): string {
        $uid = (string) $uid;

        if (! preg_match('/^[1-9][0-9]*$/', $uid)) {
            throw new RuntimeException(
                'Invalid message UID.',
            );
        }

        return $uid;
    }

    /**
     * @param array<string, mixed> $message
     * @return array<string, mixed>
     */
    private function normalizeSummaryMessage(
        array $message,
    ): array {
        $flags = $this->normalizeFlags(
            (string) ($message['flags'] ?? ''),
        );

        $from = $this->parseAddress(
            (string) ($message['hdr.from'] ?? ''),
        );

        return [
            'mailbox' => trim(
                (string) ($message['mailbox'] ?? 'INBOX'),
            ),

            'uid' => (string) ($message['uid'] ?? ''),

            'flags' => $flags,

            'unread' => ! in_array(
                '\\Seen',
                $flags,
                true,
            ),

            'starred' => in_array(
                '\\Flagged',
                $flags,
                true,
            ),

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
            'mailbox' => trim(
                (string) ($message['mailbox'] ?? 'INBOX'),
            ),

            'uid' => (string) ($message['uid'] ?? ''),

            'flags' => $flags,

            'unread' => ! in_array(
                '\\Seen',
                $flags,
                true,
            ),

            'starred' => in_array(
                '\\Flagged',
                $flags,
                true,
            ),

            'size' => (int) (
                $message['size.virtual'] ?? 0
            ),

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
                (string) (
                    $message['imap.bodystructure'] ?? ''
                ),
            ),

            'body' => [
                'text' => $this->normalizeBody(
                    (string) ($message['body.1'] ?? ''),
                ),

                'html' => '',
            ],
            'attachments' => $this->extractAttachments(
                (string) (
                    $message['imap.bodystructure'] ?? ''
                ),
            ),
        ];
    }

    /**
     * @return array<int, string>
     */
    private function normalizeFlags(
        string $flags,
    ): array {
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
    private function parseAddress(
        string $value,
    ): array {
        $value = trim($value);

        if (
            preg_match(
                '/^(?:"?(.+?)"?\s*)?<([^<>]+)>$/',
                $value,
                $matches,
            )
        ) {
            $address = trim(
                $matches[2],
            );

            $name = trim(
                isset($matches[1])
                    ? trim(
                    $matches[1],
                    "\" \t\n\r\0\x0B",
                )
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
            str_replace(
                "\r\n",
                "\n",
                $value,
            ),
        );
    }
    private function extractAttachments(
        string $structure,
    ): array {
        if (trim($structure) === '') {
            return [];
        }

        $attachments = [];

        if (
            preg_match(
                '/"image"\s+"([^"]+)".*?"base64"\s+([0-9]+).*?"filename\*"\s+"utf-8\'\'([^"]+)"/s',
                $structure,
                $matches,
            )
        ) {
            $attachments[] = [
                'part' => '2',
                'filename' => urldecode(
                    $matches[3],
                ),
                'content_type' => 'image/' . $matches[1],
                'size' => (int) $matches[2],
            ];
        }

        return $attachments;
    }
}
