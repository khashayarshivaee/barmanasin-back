<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use App\Services\Auth\UserAccessTokenService;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource =
        UserResource::class;


    public function mount(int|string $record): void
    {
        parent::mount($record);

        $token = session()->pull(
            'new_user_activation_token'
        );

        $userId = session()->pull(
            'new_user_activation_user_id'
        );

        if (
            filled($token)
            && (int) $userId === (int) $this->record->getKey()
        ) {
            $this->showAccessLink(
                token: $token,
                type: 'activation'
            );
        }
    }


    public function getHeading(): string
    {
        return 'Manage User';
    }


    public function getSubheading(): ?string
    {
        /** @var User $user */
        $user = $this->record;

        return $user->mailbox_address
            ?: $user->email;
    }


    protected function getHeaderActions(): array
    {
        return [

            Action::make('activationLink')
                ->label('New Activation Link')
                ->icon('heroicon-m-link')
                ->color('gray')
                ->visible(
                    fn (): bool =>
                    ! $this->record->isActivated()
                )
                ->requiresConfirmation()
                ->modalHeading('Generate new activation link?')
                ->modalDescription(
                    'Any previous unused activation link for this user will be revoked.'
                )
                ->action(function (): void {
                    /** @var User $user */
                    $user = $this->record;

                    $result = app(
                        UserAccessTokenService::class
                    )->issueActivationToken(
                        user: $user,
                        createdBy: auth()->user()
                    );

                    $this->showAccessLink(
                        token: $result['token'],
                        type: 'activation'
                    );
                }),


            Action::make('resetAccess')
                ->label('Reset Access')
                ->icon('heroicon-m-key')
                ->color('warning')
                ->visible(
                    fn (): bool =>
                        $this->record->isActivated()
                        && ! $this->record->isSuspended()
                )
                ->requiresConfirmation()
                ->modalHeading('Reset user access?')
                ->modalDescription(
                    'A new one-time password reset link will be generated.'
                )
                ->action(function (): void {
                    /** @var User $user */
                    $user = $this->record;

                    $result = app(
                        UserAccessTokenService::class
                    )->issuePasswordResetToken(
                        user: $user,
                        createdBy: auth()->user()
                    );

                    $this->showAccessLink(
                        token: $result['token'],
                        type: 'password-reset'
                    );
                }),


            Action::make('suspend')
                ->label('Suspend')
                ->icon('heroicon-m-no-symbol')
                ->color('danger')
                ->visible(
                    fn (): bool =>
                        ! $this->record->isSuspended()
                        && auth()->id() !== $this->record->getKey()
                )
                ->requiresConfirmation()
                ->modalHeading('Suspend this user?')
                ->modalDescription(
                    'The user will no longer be able to sign in. Mailbox data will remain intact.'
                )
                ->action(function (): void {
                    /** @var User $user */
                    $user = $this->record;

                    $user->forceFill([
                        'is_active' => false,
                        'suspended_at' => now(),
                    ])->save();

                    if ($user->mailbox) {
                        $user->mailbox->forceFill([
                            'status' => 'suspended',
                        ])->save();
                    }

                    Notification::make()
                        ->title('User suspended')
                        ->success()
                        ->send();

                    $this->refreshFormData([
                        'is_active',
                        'suspended_at',
                    ]);
                }),


            Action::make('reactivate')
                ->label('Reactivate')
                ->icon('heroicon-m-check-circle')
                ->color('success')
                ->visible(
                    fn (): bool =>
                    $this->record->isSuspended()
                )
                ->requiresConfirmation()
                ->action(function (): void {
                    /** @var User $user */
                    $user = $this->record;

                    $user->forceFill([
                        'is_active' => true,
                        'suspended_at' => null,
                    ])->save();

                    if ($user->mailbox_enabled && $user->mailbox) {
                        $user->mailbox->forceFill([
                            'status' => $user->isActivated()
                            && ! $user->must_change_password
                                ? 'active'
                                : 'pending',
                        ])->save();
                    }

                    Notification::make()
                        ->title('User reactivated')
                        ->success()
                        ->send();

                    $this->refreshFormData([
                        'is_active',
                        'suspended_at',
                    ]);
                }),


            DeleteAction::make()
                ->visible(
                    fn (): bool =>
                        auth()->id() !== $this->record->getKey()
                ),
        ];
    }


    private function showAccessLink(
        string $token,
        string $type
    ): void {
        $path = match ($type) {
            'password-reset' => 'reset-access',
            default => 'activate',
        };

        $url = url(
            "/{$path}/{$token}"
        );

        Notification::make()
            ->title(
                $type === 'password-reset'
                    ? 'Password reset link generated'
                    : 'Activation link generated'
            )
            ->body(
                "This link is shown only now. Copy it and send it securely to the user:\n\n`{$url}`"
            )
            ->warning()
            ->persistent()
            ->send();
    }
}
