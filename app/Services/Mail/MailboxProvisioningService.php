<?php

namespace App\Services\Mail;

use App\Models\Mailbox;
use App\Services\Mail\Providers\MailProviderInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MailboxProvisioningService
{
    public function __construct(
        protected MailProviderInterface $provider
    ) {
    }


    public function provision(
        Mailbox $mailbox
    ): Mailbox {

        return DB::transaction(function () use ($mailbox) {

            $result = $this->provider
                ->provision($mailbox);

            Log::info('Mailbox provision result', [
                'mailbox_id' => $mailbox->id,
                'address' => $mailbox->address,
                'result' => $result,
            ]);


            $mailbox->update([

                'external_id' =>
                    $result['id'] ?? null,

                'status' =>
                    'active',

            ]);


            return $mailbox->refresh();
        });
    }


    public function suspend(
        Mailbox $mailbox
    ): Mailbox {

        $this->provider
            ->suspend($mailbox);


        $mailbox->update([
            'status' => 'suspended',
        ]);


        return $mailbox->refresh();
    }


    public function activate(
        Mailbox $mailbox
    ): Mailbox {

        $this->provider
            ->activate($mailbox);


        $mailbox->update([
            'status' => 'active',
        ]);


        return $mailbox->refresh();
    }


    public function delete(
        Mailbox $mailbox
    ): void {

        $this->provider
            ->delete($mailbox);


        $mailbox->delete();
    }
}
