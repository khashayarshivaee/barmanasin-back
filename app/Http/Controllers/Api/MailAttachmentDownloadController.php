<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Mail\MailboxReaderService;
use Illuminate\Http\Request;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

class MailAttachmentDownloadController extends Controller
{
    public function __invoke(
        Request $request,
        string $uid,
        string $part,
        MailboxReaderService $mailboxReader,
    ): Response {
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

        $folder = (string) $request->query(
            'folder',
            'INBOX',
        );

        try {
            $message = $mailboxReader->message(
                $user->mailbox_address,
                $uid,
                $folder,
            );

            if ($message === null) {
                return response()->json([
                    'message' => 'Message not found.',
                ], 404);
            }

            $attachment = collect(
                $message['attachments'] ?? [],
            )->first(
                static fn (array $attachment): bool =>
                    (string) (
                        $attachment['part'] ?? ''
                    ) === $part,
            );

            if (! is_array($attachment)) {
                return response()->json([
                    'message' => 'Attachment not found.',
                ], 404);
            }

            $content = $mailboxReader->messagePart(
                $user->mailbox_address,
                $uid,
                $part,
                $folder,
            );

            if ($content === '') {
                return response()->json([
                    'message' => 'Attachment not found.',
                ], 404);
            }

            $decoded = $this->decodeContent(
                $content,
                (string) (
                    $attachment['encoding'] ?? ''
                ),
            );

            $filename = trim(
                (string) (
                    $attachment['filename']
                    ?? 'attachment'
                ),
            );

            if ($filename === '') {
                $filename = 'attachment';
            }

            $contentType = trim(
                (string) (
                    $attachment['content_type']
                    ?? 'application/octet-stream'
                ),
            );

            if ($contentType === '') {
                $contentType =
                    'application/octet-stream';
            }

            return response(
                $decoded,
                200,
                [
                    'Content-Type' =>
                        $contentType,

                    'Content-Disposition' =>
                        'inline; filename="' .
                        addcslashes(
                            $filename,
                            "\"\\",
                        ) .
                        '"',

                    'X-Content-Type-Options' =>
                        'nosniff',

                    'Cache-Control' =>
                        'private, max-age=3600',
                ],
            );
        } catch (RuntimeException $exception) {
            report($exception);

            return response()->json([
                'message' =>
                    'Unable to load attachment.',
            ], 503);
        }
    }

    private function decodeContent(
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

            if ($decoded === false) {
                throw new RuntimeException(
                    'Unable to decode attachment.',
                );
            }

            return $decoded;
        }

        if ($encoding === 'quoted-printable') {
            return quoted_printable_decode(
                $content,
            );
        }

        return $content;
    }
}
