<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Mail\MailboxReaderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RuntimeException;

class MailDraftDeleteController extends Controller
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
                'message' =>
                    'This mail account is not available.',
            ], 403);
        }


        /*
         * Make sure the Draft really exists
         * before attempting to delete it.
         */
        try {

            $draft =
                $mailboxReader->message(
                    $user->mailbox_address,
                    $uid,
                    'Drafts',
                );

        } catch (RuntimeException $exception) {

            report($exception);


            return response()->json([
                'message' =>
                    'Unable to load draft.',
            ], 503);

        }


        if ($draft === null) {

            return response()->json([
                'message' =>
                    'Draft not found.',
            ], 404);

        }


        try {

            $mailboxReader->deleteDraft(
                $user->mailbox_address,
                $uid,
            );

        } catch (RuntimeException $exception) {

            report($exception);


            return response()->json([
                'message' =>
                    'Unable to delete draft.',
            ], 503);

        }


        return response()->json([
            'message' =>
                'Draft deleted successfully.',

            'deleted' =>
                true,
        ]);
    }
}
