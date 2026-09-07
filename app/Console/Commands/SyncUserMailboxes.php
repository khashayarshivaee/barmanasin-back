<?php

namespace App\Console\Commands;

use App\Models\Mailbox;
use App\Models\User;
use Illuminate\Console\Command;

class SyncUserMailboxes extends Command
{
    protected $signature = 'mailboxes:sync';

    protected $description = 'Create and synchronize local mailbox records for users';


    public function handle(): int
    {
        User::query()
            ->where('mailbox_enabled', true)
            ->whereNotNull('mailbox_address')
            ->chunkById(100, function ($users): void {
                foreach ($users as $user) {

                    [$localPart, $domain] = explode(
                        '@',
                        $user->mailbox_address,
                        2
                    );


                    $mailbox = Mailbox::query()->firstOrCreate(
                        [
                            'address' => $user->mailbox_address,
                        ],
                        [
                            'user_id' => $user->id,

                            'local_part' => $localPart,

                            'domain' => $domain,

                            'provider' => 'local',

                            'quota_mb' => $user->mailbox_quota_mb,

                            'used_storage_mb' => 0,

                            /*
                             * Mailbox lifecycle:
                             *
                             * pending:
                             * User exists but activation is not completed.
                             *
                             * active:
                             * Activation flow changes this state.
                             *
                             * suspended:
                             * User suspension changes this state.
                             */
                            'status' => 'pending',
                        ]
                    );


                    /*
                     * Keep mailbox metadata synchronized.
                     *
                     * Do not touch status here.
                     * Status is controlled by user lifecycle events:
                     * - ActivationController
                     * - Suspend/Reactivate actions
                     */
                    $mailbox->forceFill([
                        'user_id' => $user->id,

                        'local_part' => $localPart,

                        'domain' => $domain,

                        'provider' => 'local',

                        'quota_mb' => $user->mailbox_quota_mb,
                    ])->save();


                    if ($mailbox->wasRecentlyCreated) {
                        $this->info(
                            "Mailbox created: {$user->mailbox_address}"
                        );
                    } else {
                        $this->line(
                            "Mailbox synchronized: {$user->mailbox_address}"
                        );
                    }
                }
            });


        $this->info(
            'Mailbox synchronization completed.'
        );


        return self::SUCCESS;
    }
}
