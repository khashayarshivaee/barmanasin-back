<?php

namespace App\Filament\Resources\SiteFooters\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SiteFootersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('description_en')
                    ->label('Description')
                    ->limit(60)
                    ->wrap()
                    ->placeholder('—'),

                TextColumn::make('email')
                    ->label('Email')
                    ->copyable()
                    ->placeholder('—'),

                TextColumn::make('phone')
                    ->label('Phone')
                    ->placeholder('—'),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime('M j, Y — H:i')
                    ->sortable(),

            ])

            ->recordActions([
                EditAction::make(),
            ])

            ->defaultSort(
                'updated_at',
                'desc'
            );
    }
}
