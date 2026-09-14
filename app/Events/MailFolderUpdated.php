<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;


class MailFolderUpdated implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;


    public function __construct(
        public int $userId,
        public array $folders,
    ) {
    }


    public function broadcastOn(): array
    {
        return [
            new PrivateChannel(
                'mailbox.' . $this->userId
            ),
        ];
    }


    public function broadcastAs(): string
    {
        return 'folder.updated';
    }
}
