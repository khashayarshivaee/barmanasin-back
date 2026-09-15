<?php

namespace App\Services\Mail;

use App\Events\MailMessageReceived;
use App\Models\Mailbox;
use App\Models\MailboxWatchState;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;


class MailWatcherService
{
    public function __construct()
    {
    }


    public function scan(): void
    {
        Mailbox::query()
            ->where('status', 'active')
            ->chunkById(100, function ($mailboxes) {

                foreach ($mailboxes as $mailbox) {

                    $this->scanMailbox($mailbox);

                }

            });
    }


    private function scanMailbox(
        Mailbox $mailbox
    ): void {

        $path = $this->mailboxNewPath($mailbox);


        if (
            ! File::isDirectory($path)
        ) {
            return;
        }


        $hasPreviousState =
            MailboxWatchState::query()
                ->where(
                    'mailbox_id',
                    $mailbox->id
                )
                ->exists();


        $newMessageDetected = false;


        foreach (
            File::files($path)
            as $file
        ) {

            $messageKey = $file->getFilename();


            $alreadySeen =
                MailboxWatchState::query()
                    ->where(
                        'mailbox_id',
                        $mailbox->id
                    )
                    ->where(
                        'message_key',
                        $messageKey
                    )
                    ->exists();


            if ($alreadySeen) {
                continue;
            }


            MailboxWatchState::create([
                'mailbox_id' => $mailbox->id,

                'message_key' => $messageKey,

                'detected_at' => Carbon::now(),
            ]);


            /*
             * First scan:
             *
             * Only create baseline.
             * Do not notify user about old messages.
             */
            if ($hasPreviousState) {

                $newMessageDetected = true;

            }
        }


        if (
            $newMessageDetected
        ) {

            broadcast(
                new MailMessageReceived(
                    $mailbox->user_id,
                ),
            );

        }
    }


    private function mailboxNewPath(
        Mailbox $mailbox
    ): string {

        return sprintf(
            '/var/vmail/%s/%s/Maildir/new',

            $mailbox->domain,

            $mailbox->local_part,
        );
    }
}
