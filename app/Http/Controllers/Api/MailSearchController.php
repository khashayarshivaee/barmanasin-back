<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Mail\MailboxReaderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RuntimeException;

class MailSearchController extends Controller
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

        $query = trim(
            (string) $request->query('q', ''),
        );

        $type = strtoupper(
            (string) $request->query('type', 'TEXT'),
        );

        if ($query === '') {
            return response()->json([
                'message' => 'Search query is required.',
            ], 422);
        }

        try {
            $messages = $mailboxReader->search(
                $user->mailbox_address,
                $query,
                $type,
            );
        } catch (RuntimeException $exception) {
            report($exception);

            return response()->json([
                'message' => 'Unable to search mailbox.',
            ], 503);
        }

        return response()->json([
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

                'search' => $query,
            ],
        ]);
    }
}
