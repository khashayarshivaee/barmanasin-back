<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Mail\MailboxReaderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RuntimeException;

class MailFolderStatsController extends Controller
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
                'message' =>
                    'This mail account is not available.',
            ], 403);
        }


        try {

            $folders = [

                'inbox' => $mailboxReader->folderCount(
                    $user->mailbox_address,
                    'INBOX',
                ),


                'sent' => $mailboxReader->folderCount(
                    $user->mailbox_address,
                    'Sent',
                ),


                'drafts' => $mailboxReader->folderCount(
                    $user->mailbox_address,
                    'Drafts',
                ),


                'archive' => $mailboxReader->folderCount(
                    $user->mailbox_address,
                    'Archive',
                ),


                'trash' => $mailboxReader->folderCount(
                    $user->mailbox_address,
                    'Trash',
                ),


                'starred' => $mailboxReader->starredCount(
                    $user->mailbox_address,
                ),

            ];


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
