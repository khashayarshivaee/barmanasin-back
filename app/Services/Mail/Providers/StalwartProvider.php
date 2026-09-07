<?php

namespace App\Services\Mail\Providers;

use App\Models\Mailbox;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class StalwartProvider implements MailProviderInterface
{
    public function provision(Mailbox $mailbox): array
    {
        $response = $this->client()
            ->post('/api/accounts', [

                'name' => $mailbox->address,

                'description' =>
                    "Barmanasin mailbox",

                'quota' =>
                    $mailbox->quota_mb,

            ]);


        if ($response->failed()) {

            throw new RuntimeException(
                'Stalwart mailbox provisioning failed: '
                . $response->body()
            );
        }


        return $response->json();
    }


    public function suspend(Mailbox $mailbox): bool
    {
        /*
         * Later:
         * Suspend mailbox in Stalwart
         */

        return true;
    }


    public function activate(Mailbox $mailbox): bool
    {
        /*
         * Later:
         * Activate mailbox in Stalwart
         */

        return true;
    }


    public function delete(Mailbox $mailbox): bool
    {
        /*
         * Later:
         * Delete mailbox in Stalwart
         */

        return true;
    }


    public function resetPassword(
        Mailbox $mailbox,
        string $password
    ): bool {
        /*
         * Later:
         * Update mailbox password
         */

        return true;
    }

    protected function client()
    {
        return Http::baseUrl(
            config('mailserver.stalwart.url')
        )
            ->withToken(
                config('mailserver.stalwart.token')
            )
            ->acceptJson();
    }
}
