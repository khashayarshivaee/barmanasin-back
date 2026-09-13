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

        $page = max(
            1,
            (int) $request->query('page', 1),
        );

        $perPage = min(
            100,
            max(
                1,
                (int) $request->query('per_page', 100),
            ),
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
                $page,
                $perPage,
            );
            $page = max(
                1,
                (int) $request->query('page', 1),
            );

            $perPage = min(
                100,
                max(
                    1,
                    (int) $request->query('per_page', 100),
                ),
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

                'page' => $page,

                'per_page' => $perPage,

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
