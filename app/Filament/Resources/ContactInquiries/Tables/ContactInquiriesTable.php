<?php

namespace App\Filament\Resources\ContactInquiries\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ContactInquiriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->limit(40),

                TextColumn::make('company')
                    ->label('Company')
                    ->placeholder('—')
                    ->searchable()
                    ->limit(40)
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable()
                    ->limit(45),

                TextColumn::make('phone')
                    ->label('Phone')
                    ->placeholder('—')
                    ->copyable()
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                TextColumn::make('project_type')
                    ->label('Project Type')
                    ->placeholder('—')
                    ->limit(35)
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(
                        fn (string $state): string =>
                        match ($state) {
                            'new' => 'New',
                            'in_progress' => 'In Progress',
                            'resolved' => 'Resolved',
                            default => ucfirst($state),
                        }
                    )
                    ->color(
                        fn (string $state): string =>
                        match ($state) {
                            'new' => 'danger',
                            'in_progress' => 'warning',
                            'resolved' => 'success',
                            default => 'gray',
                        }
                    )
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Received')
                    ->dateTime('M j, Y — H:i')
                    ->timezone('Asia/Tehran')
                    ->sortable(),

            ])

            ->filters([

                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'new' => 'New',
                        'in_progress' => 'In Progress',
                        'resolved' => 'Resolved',
                    ]),

            ])

            ->recordActions([

                EditAction::make()
                    ->label('Open'),

            ])

            ->defaultSort(
                'created_at',
                'desc'
            );
    }
}
