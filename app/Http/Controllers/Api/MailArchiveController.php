<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Mail\MailboxReaderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RuntimeException;

class MailArchiveController extends Controller
{
    public function __invoke(
        Request $request,
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

        try {
            $messages = $mailboxReader->archive(
                $user->mailbox_address,
            );
        } catch (RuntimeException $exception) {
            report($exception);

            return response()->json([
                'message' => 'Unable to load archived messages.',
            ], 503);
        }

        return response()->json([
            'mailbox' => [
                'address' => $user->mailbox_address,
                'quota_mb' => $user->mailbox_quota_mb,
            ],

            'messages' => $messages,

            'meta' => [
                'total' => count($messages),

                'unread' => count(
                    array_filter(
                        $messages,
                        static fn (array $message): bool =>
                        (bool) ($message['unread'] ?? false),
                    ),
                ),
            ],
        ]);
    }
}
