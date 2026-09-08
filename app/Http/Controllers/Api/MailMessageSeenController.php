<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Mail\MailboxReaderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RuntimeException;

class MailMessageSeenController extends Controller
{
    public function __invoke(
        Request $request,
        string $uid,
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
                'message' => 'This mail account is not available.',
            ], 403);
        }

        if (! preg_match('/^[1-9][0-9]*$/', $uid)) {
            return response()->json([
                'message' => 'Invalid message UID.',
            ], 422);
        }

        $validated = $request->validate([
            'seen' => [
                'required',
                'boolean',
            ],

            'folder' => [
                'sometimes',
                'string',
                'in:INBOX,Archive,Trash,Sent,Drafts',
            ],
        ]);

        $folder = $validated['folder'] ?? 'INBOX';

        try {
            $message = $validated['seen']
                ? $mailboxReader->markSeen(
                    $user->mailbox_address,
                    $uid,
                    $folder,
                )
                : $mailboxReader->markUnseen(
                    $user->mailbox_address,
                    $uid,
                    $folder,
                );
        } catch (RuntimeException $exception) {
            report($exception);

            return response()->json([
                'message' => 'Unable to update message state.',
            ], 503);
        }

        return response()->json([
            'message' => $message,
            'seen' => ! $message['unread'],
        ]);
    }
}
