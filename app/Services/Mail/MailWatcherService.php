<?php

namespace App\Services\Mail;

use App\Models\Mailbox;
use App\Models\MailboxWatchState;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Carbon;


class MailWatcherService
{
    public function __construct(
        private readonly MailFolderBroadcastService $folderBroadcast,
    ) {
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


    private function scanMailbox(Mailbox $mailbox): void
    {
        $path = $this->mailboxNewPath($mailbox);


        if (
            ! File::isDirectory($path)
        ) {
            return;
        }


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


            $this->folderBroadcast
                ->broadcastFor(
                    $mailbox->user
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
