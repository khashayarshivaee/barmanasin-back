<?php

namespace App\Console\Commands;

use App\Models\Mailbox;
use App\Services\Mail\MailboxProvisioningService;
use Illuminate\Console\Command;

class ProvisionMailbox extends Command
{
    protected $signature = 'mailbox:provision {mailbox}';

    protected $description = 'Provision a mailbox on the mail provider';


    public function handle(
        MailboxProvisioningService $service
    ): int {

        $mailbox = Mailbox::query()
            ->find($this->argument('mailbox'));


        if (! $mailbox) {

            $this->error(
                'Mailbox not found.'
            );

            return self::FAILURE;
        }


        if ($mailbox->status === 'active') {

            $this->warn(
                'Mailbox is already active.'
            );

            return self::SUCCESS;
        }


        $this->info(
            "Provisioning {$mailbox->address} ..."
        );


        try {

            $service->provision($mailbox);


            $this->info(
                'Mailbox provisioned successfully.'
            );


        } catch (\Throwable $exception) {

            $this->error(
                $exception->getMessage()
            );

            return self::FAILURE;
        }


        return self::SUCCESS;
    }
}
