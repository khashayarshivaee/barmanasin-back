<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Mail\MailboxReaderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RuntimeException;

class MailMessageTrashController extends Controller
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
            'folder' => [
                'sometimes',
                'string',
                'in:INBOX,Archive,Sent,Drafts',
            ],
        ]);

        $folder = $validated['folder'] ?? 'INBOX';

        try {
            $mailboxReader->trashMessage(
                $user->mailbox_address,
                $uid,
                $folder,
            );
        } catch (RuntimeException $exception) {
            report($exception);

            return response()->json([
                'message' => 'Unable to move message to Trash.',
            ], 503);
        }

        return response()->json([
            'message' => 'Message moved to Trash.',
            'trashed' => true,

            'source' => [
                'mailbox' => $folder,
                'uid' => $uid,
            ],

            'destination' => [
                'mailbox' => 'Trash',
            ],
        ]);
    }
}
