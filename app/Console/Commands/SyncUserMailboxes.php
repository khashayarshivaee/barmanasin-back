<?php

namespace App\Console\Commands;

use App\Models\Mailbox;
use App\Models\User;
use Illuminate\Console\Command;

class SyncUserMailboxes extends Command
{
    protected $signature = 'mailboxes:sync';

    protected $description = 'Create local mailbox records for existing users';


    public function handle(): int
    {
        User::query()
            ->where('mailbox_enabled', true)
            ->whereNotNull('mailbox_address')
            ->chunkById(100, function ($users) {

                foreach ($users as $user) {

                    [$localPart, $domain] = explode(
                        '@',
                        $user->mailbox_address,
                        2
                    );


                    Mailbox::query()->firstOrCreate(
                        [
                            'address' => $user->mailbox_address,
                        ],
                        [
                            'user_id' => $user->id,

                            'local_part' => $localPart,

                            'domain' => $domain,

                            'provider' => 'stalwart',

                            'quota_mb' => $user->mailbox_quota_mb,

                            'used_storage_mb' => 0,

                            'status' => 'pending',
                        ]
                    );


                    $this->info(
                        "Mailbox created: {$user->mailbox_address}"
                    );
                }
            });


        $this->info(
            'Mailbox synchronization completed.'
        );


        return self::SUCCESS;
    }
}
