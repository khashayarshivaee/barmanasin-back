<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Mail\MailboxReaderService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use RuntimeException;

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

        $folder = $request->query(
            'folder',
            'INBOX',
        );

        try {
            $content = $mailboxReader->messagePart(
                $user->mailbox_address,
                $uid,
                $part,
                $folder,
            );

        } catch (RuntimeException $exception) {
            report($exception);

            return response()->json([
                'message' => 'Unable to load attachment.',
            ], 503);
        }

        if ($content === '') {
            return response()->json([
                'message' => 'Attachment not found.',
            ], 404);
        }

        return response(
            base64_decode($content),
            200,
            [
                'Content-Type' => 'application/octet-stream',
                'Content-Disposition' => 'attachment',
            ],
        );
    }
}
