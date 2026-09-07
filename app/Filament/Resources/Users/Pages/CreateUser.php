<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Models\Mailbox;
use App\Models\User;
use App\Services\Auth\UserAccessTokenService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CreateUser extends CreateRecord
{
    protected static string $resource =
        UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $username = Str::lower(
            trim((string) ($data['mailbox_username'] ?? ''))
        );

        $email = "{$username}@barmanasin.com";

        $exists = User::query()
            ->where('email', $email)
            ->orWhere('mailbox_address', $email)
            ->exists();

        $mailboxExists = Mailbox::query()
            ->where('address', $email)
            ->exists();

        if ($exists || $mailboxExists) {
            throw ValidationException::withMessages([
                'mailbox_username' =>
                    'This company email address is already in use.',
            ]);
        }

        unset($data['mailbox_username']);

        $data['email'] = $email;

        $data['mailbox_address'] =
            ($data['mailbox_enabled'] ?? true)
                ? $email
                : null;

        /*
         * Admin never chooses or sees the user's password.
         * This placeholder becomes useless after activation.
         */
        $data['password'] = Str::random(64);

        $data['activated_at'] = null;
        $data['suspended_at'] = null;
        $data['must_change_password'] = true;
        $data['mailbox_external_id'] = null;

        return $data;
    }

    protected function afterCreate(): void
    {
        /** @var User $record */
        $record = $this->record;

        /*
         * Register the mailbox in Barmanasin.
         *
         * The local mail engine will serve this mailbox.
         * It remains pending until the mail infrastructure
         * is ready to activate it.
         */
        if (
            $record->mailbox_enabled
            && filled($record->mailbox_address)
        ) {
            [$localPart, $domain] = explode(
                '@',
                $record->mailbox_address,
                2
            );

            Mailbox::query()->firstOrCreate(
                [
                    'address' => $record->mailbox_address,
                ],
                [
                    'user_id' => $record->getKey(),
                    'local_part' => $localPart,
                    'domain' => $domain,
                    'provider' => 'local',
                    'external_id' => null,
                    'quota_mb' => $record->mailbox_quota_mb,
                    'used_storage_mb' => 0,
                    'status' => 'pending',
                ]
            );
        }

        /*
         * Generate one-time account activation access.
         */
        $result = app(
            UserAccessTokenService::class
        )->issueActivationToken(
            user: $record,
            createdBy: auth()->user()
        );

        /*
         * Raw token is never persisted.
         * It survives only until the next request.
         */
        session()->flash(
            'new_user_activation_token',
            $result['token']
        );

        session()->flash(
            'new_user_activation_user_id',
            $record->getKey()
        );
    }

    protected function getRedirectUrl(): string
    {
        return static::$resource::getUrl(
            'edit',
            [
                'record' => $this->record,
            ]
        );
    }
}
