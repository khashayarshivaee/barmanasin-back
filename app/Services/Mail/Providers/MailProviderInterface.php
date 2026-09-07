<?php

namespace App\Services\Mail\Providers;

use App\Models\Mailbox;

interface MailProviderInterface
{
    public function provision(Mailbox $mailbox): array;


    public function suspend(Mailbox $mailbox): bool;


    public function activate(Mailbox $mailbox): bool;


    public function delete(Mailbox $mailbox): bool;


    public function resetPassword(
        Mailbox $mailbox,
        string $password
    ): bool;
}
