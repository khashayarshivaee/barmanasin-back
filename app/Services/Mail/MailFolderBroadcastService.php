<?php

namespace App\Services\Mail;

use App\Events\MailFolderUpdated;
use App\Models\User;

class MailFolderBroadcastService
{
    public function __construct(
        private readonly MailFolderStatsService $folderStats,
    ) {
    }


    public function broadcastFor(User $user): void
    {
        if (
            blank($user->mailbox_address)
        ) {
            return;
        }


        broadcast(
            new MailFolderUpdated(
                $user->id,
                $this->folderStats->get(
                    $user->mailbox_address,
                ),
            ),
        );
    }
}
