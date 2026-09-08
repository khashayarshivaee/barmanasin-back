<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Mail\MailboxReaderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RuntimeException;

class MailMessageController extends Controller
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

        try {
            $message = $mailboxReader->message(
                $user->mailbox_address,
                $uid,
            );
        } catch (RuntimeException $exception) {
            report($exception);

            return response()->json([
                'message' => 'Unable to load message.',
            ], 503);
        }

        if ($message === null) {
            return response()->json([
                'message' => 'Message not found.',
            ], 404);
        }

        return response()->json([
            'message' => $message,
        ]);
    }
}
