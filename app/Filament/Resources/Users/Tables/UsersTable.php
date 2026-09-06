<?php

namespace App\Filament\Resources\Users\Tables;

use App\Models\User;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('name')
                    ->label('User')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->description(
                        fn (User $record): string =>
                        $record->mailbox_address
                            ?: $record->email
                    ),

                TextColumn::make('role')
                    ->label('Role')
                    ->badge()
                    ->formatStateUsing(
                        fn (?string $state): string =>
                        match ($state) {
                            User::ROLE_SUPER_ADMIN => 'Super Admin',
                            User::ROLE_MAIL_USER => 'Mail User',
                            default => 'Unknown',
                        }
                    )
                    ->color(
                        fn (?string $state): string =>
                        match ($state) {
                            User::ROLE_SUPER_ADMIN => 'info',
                            User::ROLE_MAIL_USER => 'gray',
                            default => 'gray',
                        }
                    ),

                TextColumn::make('is_active')
                    ->label('Account')
                    ->badge()
                    ->formatStateUsing(
                        function ($state, User $record): string {
                            if ($record->isSuspended()) {
                                return 'Suspended';
                            }

                            return $state
                                ? 'Active'
                                : 'Inactive';
                        }
                    )
                    ->color(
                        function ($state, User $record): string {
                            if ($record->isSuspended()) {
                                return 'danger';
                            }

                            return $state
                                ? 'success'
                                : 'gray';
                        }
                    ),

                TextColumn::make('activated_at')
                    ->label('Activation')
                    ->badge()
                    ->formatStateUsing(
                        fn ($state): string =>
                        $state
                            ? 'Activated'
                            : 'Pending'
                    )
                    ->color(
                        fn ($state): string =>
                        $state
                            ? 'success'
                            : 'warning'
                    ),

                IconColumn::make('mailbox_enabled')
                    ->label('Mailbox')
                    ->boolean()
                    ->alignCenter(),

                TextColumn::make('mailbox_quota_mb')
                    ->label('Quota')
                    ->formatStateUsing(
                        fn ($state): string =>
                            number_format(
                                ((int) $state) / 1024,
                                1
                            ) . ' GB'
                    )
                    ->alignCenter(),

                TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime('M j, Y — H:i')
                    ->sortable()
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

            ])

            ->recordActions([
                EditAction::make()
                    ->label('Manage'),
            ])

            ->defaultSort('created_at', 'desc')

            ->emptyStateHeading('No users yet')
            ->emptyStateDescription(
                'Create the first company mail user.'
            )
            ->emptyStateIcon('heroicon-o-users');
    }
}
