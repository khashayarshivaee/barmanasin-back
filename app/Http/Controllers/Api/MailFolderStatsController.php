<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RuntimeException;
use App\Services\Mail\MailFolderStatsService;

class MailFolderStatsController extends Controller
{
    public function __invoke(
        Request $request,
        MailFolderStatsService $folderStats,
    ): JsonResponse {

        $user = $request->user();


        if (
            ! $user
            || ! $user->isMailUser()
            || ! $user->mailboxIsReady()
            || blank($user->mailbox_address)
        ) {
            return response()->json([
                'message' =>
                    'This mail account is not available.',
            ], 403);
        }


        try {

            $folders = $folderStats->get(
                $user->mailbox_address,
            );

        } catch (RuntimeException $exception) {

            report($exception);


            return response()->json([
                'message' =>
                    'Unable to load folder statistics.',
            ], 503);

        }


        return response()->json([
            'folders' => $folders,
        ]);

    }
}
