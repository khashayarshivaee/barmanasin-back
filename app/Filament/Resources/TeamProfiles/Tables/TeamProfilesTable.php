<?php

namespace App\Filament\Resources\TeamProfiles\Tables;

use App\Models\TeamProfile;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TeamProfilesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                ImageColumn::make('photo_path')
                    ->label('Photo')
                    ->getStateUsing(
                        fn (TeamProfile $record): ?string =>
                        $record->photoUrl()
                    )
                    ->circular()
                    ->imageSize(48),

                TextColumn::make('user.name')
                    ->label('Mail User')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->description(
                        fn (TeamProfile $record): ?string =>
                            $record->user?->mailbox_address
                            ?? $record->user?->email
                    ),

                TextColumn::make('name_en')
                    ->label('Public Name')
                    ->searchable()
                    ->sortable()
                    ->placeholder('—')
                    ->description(
                        fn (TeamProfile $record): ?string =>
                        $record->job_title_en
                            ?: null
                    ),

                TextColumn::make('approval_status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(
                        fn (string $state): string =>
                        match ($state) {
                            TeamProfile::STATUS_PENDING =>
                            'Pending Approval',

                            TeamProfile::STATUS_APPROVED =>
                            'Approved',

                            TeamProfile::STATUS_REJECTED =>
                            'Rejected',

                            default =>
                            'Draft',
                        }
                    )
                    ->color(
                        fn (string $state): string =>
                        match ($state) {
                            TeamProfile::STATUS_APPROVED =>
                            'success',

                            TeamProfile::STATUS_PENDING =>
                            'warning',

                            TeamProfile::STATUS_REJECTED =>
                            'danger',

                            default =>
                            'gray',
                        }
                    )
                    ->sortable(),

                TextColumn::make('is_public')
                    ->label('Visibility')
                    ->badge()
                    ->formatStateUsing(
                        fn (bool $state): string =>
                        $state
                            ? 'Public Requested'
                            : 'Private'
                    )
                    ->color(
                        fn (bool $state): string =>
                        $state
                            ? 'info'
                            : 'gray'
                    ),

                TextColumn::make('sort_order')
                    ->label('Order')
                    ->sortable(),

                TextColumn::make('submitted_at')
                    ->label('Submitted')
                    ->dateTime('M j, Y — H:i')
                    ->timezone('Asia/Tehran')
                    ->sortable()
                    ->placeholder('—'),

                TextColumn::make('approved_at')
                    ->label('Approved')
                    ->dateTime('M j, Y — H:i')
                    ->timezone('Asia/Tehran')
                    ->sortable()
                    ->placeholder('—')
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                TextColumn::make('approvedBy.name')
                    ->label('Approved By')
                    ->placeholder('—')
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime('M j, Y — H:i')
                    ->timezone('Asia/Tehran')
                    ->sortable()
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

            ])

            ->filters([

                SelectFilter::make('approval_status')
                    ->label('Approval Status')
                    ->options([
                        TeamProfile::STATUS_DRAFT =>
                            'Draft',

                        TeamProfile::STATUS_PENDING =>
                            'Pending Approval',

                        TeamProfile::STATUS_APPROVED =>
                            'Approved',

                        TeamProfile::STATUS_REJECTED =>
                            'Rejected',
                    ]),

                SelectFilter::make('is_public')
                    ->label('Visibility')
                    ->options([
                        1 => 'Public Requested',
                        0 => 'Private',
                    ]),

            ])

            ->recordActions([

                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Approve Team Profile')
                    ->modalDescription(
                        'This profile will become eligible for the public Our Team section.'
                    )
                    ->visible(
                        fn (TeamProfile $record): bool =>
                            $record->is_public
                            && ! $record->isApproved()
                    )
                    ->action(
                        function (TeamProfile $record): void {
                            $record->approval_status =
                                TeamProfile::STATUS_APPROVED;

                            $record->approved_at =
                                now();

                            $record->approved_by =
                                auth()->id();

                            $record->save();

                            Notification::make()
                                ->title('Team profile approved')
                                ->success()
                                ->send();
                        }
                    ),

                Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Reject Team Profile')
                    ->modalDescription(
                        'The profile will not be eligible for public display.'
                    )
                    ->visible(
                        fn (TeamProfile $record): bool =>
                            $record->approval_status ===
                            TeamProfile::STATUS_PENDING
                            || $record->approval_status ===
                            TeamProfile::STATUS_APPROVED
                    )
                    ->action(
                        function (TeamProfile $record): void {
                            $record->approval_status =
                                TeamProfile::STATUS_REJECTED;

                            $record->approved_at =
                                null;

                            $record->approved_by =
                                null;

                            $record->save();

                            Notification::make()
                                ->title('Team profile rejected')
                                ->success()
                                ->send();
                        }
                    ),

                EditAction::make(),

            ])

            ->defaultSort(
                'updated_at',
                'desc'
            );
    }
}
