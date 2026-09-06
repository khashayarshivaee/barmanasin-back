<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
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

        if ($exists) {
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
         * The administrator never chooses or sees the user's password.
         *
         * A cryptographically random placeholder password is stored
         * until the user activates the account and chooses their own.
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

        $result = app(
            UserAccessTokenService::class
        )->issueActivationToken(
            user: $record,
            createdBy: auth()->user()
        );

        /*
         * The raw token must never be persisted.
         *
         * We keep it only in the session for the very next request.
         * The Edit page will turn this into the one-time activation URL.
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
