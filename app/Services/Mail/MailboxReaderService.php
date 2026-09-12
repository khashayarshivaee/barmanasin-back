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

    public function __construct(
        private readonly ImapBodyStructureParser $bodyStructureParser,
    ) {
    }

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
    public function drafts(string $mailboxAddress): array
    {
        return $this->listMessages(
            $mailboxAddress,
            'drafts-list',
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

        $bodyStructure = trim(
            (string) (
                $message['imap.bodystructure'] ?? ''
            ),
        );

        $parts =
            $this->bodyStructureParser->parts(
                $bodyStructure,
            );

        $body =
            $this->loadMessageBody(
                $mailboxAddress,
                $uid,
                $folder,
                $parts,
            );

        $attachments =
            $this->bodyStructureParser->attachments(
                $bodyStructure,
            );

        return $this->normalizeDetailedMessage(
            $message,
            $body,
            $attachments,
        );
    }

    public function messagePart(
        string $mailboxAddress,
        string|int $uid,
        string $part,
        string $folder = 'INBOX',
    ): string {
        $mailboxAddress =
            $this->normalizeMailboxAddress(
                $mailboxAddress,
            );

        $folder =
            $this->normalizeFolder(
                $folder,
            );

        $uid =
            $this->normalizeMessageUid(
                $uid,
            );

        if (
            ! preg_match(
                '/^[1-9][0-9]*(?:\.[1-9][0-9]*)*$/',
                $part,
            )
        ) {
            throw new RuntimeException(
                'Invalid message part.',
            );
        }

        $result = $this->runReaderJson([
            'message-part-get',
            $mailboxAddress,
            $folder,
            $uid,
            $part,
        ]);

        return (string) (
            $result[0]["body.$part"] ?? ''
        );
    }

    /**
     * @return array{
     *     part: string,
     *     filename: string,
     *     content_type: string,
     *     size: int,
     *     disposition: string,
     *     encoding: string,
     *     content: string
     * }
     */
    public function messageAttachment(
        string $mailboxAddress,
        string|int $uid,
        string $part,
        string $folder = 'INBOX',
    ): array {
        $mailboxAddress =
            $this->normalizeMailboxAddress(
                $mailboxAddress,
            );

        $folder =
            $this->normalizeFolder(
                $folder,
            );

        $uid =
            $this->normalizeMessageUid(
                $uid,
            );


        $message = $this->message(
            $mailboxAddress,
            $uid,
            $folder,
        );


        if ($message === null) {
            throw new RuntimeException(
                'Message not found.',
            );
        }


        $attachment = null;


        foreach (
            $message['attachments'] ?? []
            as $candidate
        ) {
            if (
                (string) (
                    $candidate['part'] ?? ''
                ) === $part
            ) {
                $attachment = $candidate;

                break;
            }
        }


        if (! is_array($attachment)) {
            throw new RuntimeException(
                'Attachment not found.',
            );
        }


        $rawContent =
            $this->messagePart(
                $mailboxAddress,
                $uid,
                $part,
                $folder,
            );


        if ($rawContent === '') {
            throw new RuntimeException(
                'Attachment content is empty.',
            );
        }


        $content =
            $this->decodePartContent(
                $rawContent,
                (string) (
                    $attachment['encoding'] ?? ''
                ),
            );


        return [
            'part' =>
                (string) (
                    $attachment['part'] ?? $part
                ),

            'filename' =>
                (string) (
                    $attachment['filename']
                    ?? 'attachment'
                ),

            'content_type' =>
                (string) (
                    $attachment['content_type']
                    ?? 'application/octet-stream'
                ),

            'size' =>
                (int) (
                    $attachment['size'] ?? 0
                ),

            'disposition' =>
                (string) (
                    $attachment['disposition'] ?? ''
                ),

            'encoding' =>
                (string) (
                    $attachment['encoding'] ?? ''
                ),

            'content' =>
                $content,
        ];
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
        $mailboxAddress =
            $this->normalizeMailboxAddress(
                $mailboxAddress,
            );

        $folder =
            $this->normalizeFolder(
                $folder,
            );

        $uid =
            $this->normalizeMessageUid(
                $uid,
            );

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
        $mailboxAddress =
            $this->normalizeMailboxAddress(
                $mailboxAddress,
            );

        $folder =
            $this->normalizeFolder(
                $folder,
            );

        $uid =
            $this->normalizeMessageUid(
                $uid,
            );

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

    public function deleteDraft(
        string $mailboxAddress,
        string|int $uid,
    ): void {
        $mailboxAddress =
            $this->normalizeMailboxAddress(
                $mailboxAddress,
            );

        $uid =
            $this->normalizeMessageUid(
                $uid,
            );

        $this->runReaderAction([
            'draft-delete',
            $mailboxAddress,
            $uid,
        ]);
    }

    /**
     * @param array<int, array<string, mixed>> $parts
     * @return array{
     *     text: string,
     *     html: string
     * }
     */
    private function loadMessageBody(
        string $mailboxAddress,
        string $uid,
        string $folder,
        array $parts,
    ): array {
        $text = '';
        $html = '';

        foreach ($parts as $part) {
            $contentType = strtolower(
                (string) (
                    $part['content_type'] ?? ''
                ),
            );

            $disposition = strtolower(
                (string) (
                    $part['disposition'] ?? ''
                ),
            );

            $filename = trim(
                (string) (
                    $part['filename'] ?? ''
                ),
            );

            if (
                $disposition === 'attachment'
                || $filename !== ''
            ) {
                continue;
            }

            if (
                $contentType !== 'text/plain'
                && $contentType !== 'text/html'
            ) {
                continue;
            }

            $partNumber = (string) (
                $part['part'] ?? ''
            );

            if ($partNumber === '') {
                continue;
            }

            $raw = $this->messagePart(
                $mailboxAddress,
                $uid,
                $partNumber,
                $folder,
            );

            $decoded =
                $this->decodePartContent(
                    $raw,
                    (string) (
                        $part['encoding'] ?? ''
                    ),
                );

            if (
                $contentType === 'text/plain'
                && $text === ''
            ) {
                $text =
                    $this->normalizeBody(
                        $decoded,
                    );
            }

            if (
                $contentType === 'text/html'
                && $html === ''
            ) {
                $html =
                    $this->normalizeBody(
                        $decoded,
                    );
            }

            if (
                $text !== ''
                && $html !== ''
            ) {
                break;
            }
        }

        return [
            'text' => $text,
            'html' => $html,
        ];
    }

    private function decodePartContent(
        string $content,
        string $encoding,
    ): string {
        $encoding = strtolower(
            trim($encoding),
        );

        if ($encoding === 'base64') {
            $decoded = base64_decode(
                preg_replace(
                    '/\s+/',
                    '',
                    $content,
                ) ?? $content,
                true,
            );

            return $decoded !== false
                ? $decoded
                : $content;
        }

        if ($encoding === 'quoted-printable') {
            return quoted_printable_decode(
                $content,
            );
        }

        return $content;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function listMessages(
        string $mailboxAddress,
        string $action,
        bool $sortByDate = false,
    ): array {
        $mailboxAddress =
            $this->normalizeMailboxAddress(
                $mailboxAddress,
            );

        $messages = $this->runReaderJson([
            $action,
            $mailboxAddress,
        ]);

        $normalized = array_map(
            fn (array $message): array =>
            $this->normalizeSummaryMessage(
                $message,
            ),
            $messages,
        );

        if ($sortByDate) {
            usort(
                $normalized,
                static function (
                    array $a,
                    array $b,
                ): int {
                    $aTime = strtotime(
                        (string) (
                            $a['date'] ?? ''
                        ),
                    ) ?: 0;

                    $bTime = strtotime(
                        (string) (
                            $b['date'] ?? ''
                        ),
                    ) ?: 0;

                    return $bTime <=> $aTime;
                },
            );

            return $normalized;
        }

        usort(
            $normalized,
            static fn (
                array $a,
                array $b,
            ): int =>
                (int) $b['uid']
                <=>
                (int) $a['uid'],
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
        $mailboxAddress =
            $this->normalizeMailboxAddress(
                $mailboxAddress,
            );

        $folder =
            $this->normalizeFolder(
                $folder,
            );

        $uid =
            $this->normalizeMessageUid(
                $uid,
            );

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
        $mailboxAddress =
            $this->normalizeMailboxAddress(
                $mailboxAddress,
            );

        $folder =
            $this->normalizeFolder(
                $folder,
            );

        $uid =
            $this->normalizeMessageUid(
                $uid,
            );

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
        $result = Process::timeout(30)
            ->run([
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

    private function normalizeFolder(
        string $folder,
    ): string {
        $folder = trim($folder);

        if (
            ! in_array(
                $folder,
                self::ALLOWED_FOLDERS,
                true,
            )
        ) {
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

        if (
            ! preg_match(
                '/^[1-9][0-9]*$/',
                $uid,
            )
        ) {
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
            (string) (
                $message['flags'] ?? ''
            ),
        );

        $from = $this->parseAddress(
            (string) (
                $message['hdr.from'] ?? ''
            ),
        );

        return [
            'mailbox' => trim(
                (string) (
                    $message['mailbox']
                    ?? 'INBOX'
                ),
            ),

            'uid' => (string) (
                $message['uid'] ?? ''
            ),

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
                (string) (
                    $message['hdr.to'] ?? ''
                ),
            ),

            'subject' =>
                $this->normalizeHeader(
                    (string) (
                        $message['hdr.subject']
                        ?? ''
                    ),
                    '(No subject)',
                ),

            'date' => trim(
                (string) (
                    $message['hdr.date']
                    ?? ''
                ),
            ),
        ];
    }

    /**
     * @param array<string, mixed> $message
     * @param array{
     *     text: string,
     *     html: string
     * } $body
     * @param array<int, array<string, mixed>> $attachments
     * @return array<string, mixed>
     */
    private function normalizeDetailedMessage(
        array $message,
        array $body,
        array $attachments,
    ): array {
        $flags = $this->normalizeFlags(
            (string) (
                $message['flags'] ?? ''
            ),
        );

        return [
            'mailbox' => trim(
                (string) (
                    $message['mailbox']
                    ?? 'INBOX'
                ),
            ),

            'uid' => (string) (
                $message['uid'] ?? ''
            ),

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
                (string) (
                    $message['hdr.from']
                    ?? ''
                ),
            ),

            'to' => trim(
                (string) (
                    $message['hdr.to']
                    ?? ''
                ),
            ),

            'cc' => trim(
                (string) (
                    $message['hdr.cc']
                    ?? ''
                ),
            ),

            'bcc' => trim(
                (string) (
                    $message['hdr.bcc']
                    ?? ''
                ),
            ),

            'reply_to' => trim(
                (string) (
                    $message['hdr.reply-to']
                    ?? ''
                ),
            ),

            'subject' =>
                $this->normalizeHeader(
                    (string) (
                        $message['hdr.subject']
                        ?? ''
                    ),
                    '(No subject)',
                ),

            'date' => trim(
                (string) (
                    $message['hdr.date']
                    ?? ''
                ),
            ),

            'message_id' => trim(
                (string) (
                    $message['hdr.message-id']
                    ?? ''
                ),
            ),

            'body_structure' => trim(
                (string) (
                    $message['imap.bodystructure']
                    ?? ''
                ),
            ),

            'body' => $body,

            'attachments' => $attachments,
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
                preg_split(
                    '/\s+/',
                    $flags,
                ) ?: [],
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
        $value = trim(
            $value,
        );

        if ($value === '') {
            return $fallback;
        }


        $decoded =
            iconv_mime_decode(
                $value,
                ICONV_MIME_DECODE_CONTINUE_ON_ERROR,
                'UTF-8',
            );


        if (
            $decoded === false
            ||
            trim($decoded) === ''
        ) {
            return $value;
        }


        return trim(
            $decoded,
        );
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
}
