<?php

namespace App\Filament\Resources\ContactInquiries\Tables;

use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
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
                    ->sortable(),

                TextColumn::make('company')
                    ->label('Company')
                    ->placeholder('—')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable()
                    ->toggleable(),

                TextColumn::make('project_type')
                    ->label('Project Type')
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(
                        fn (string $state): string => match ($state) {
                            'new' => 'New',
                            'in_progress' => 'In Progress',
                            'resolved' => 'Resolved',
                            default => ucfirst($state),
                        }
                    )
                    ->color(
                        fn (string $state): string => match ($state) {
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
