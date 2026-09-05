<?php

namespace App\Filament\Resources\HomeCapabilitiesSections\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class HomeCapabilitiesSectionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('title_en')
                    ->label('Title')
                    ->weight('medium')
                    ->searchable()
                    ->limit(60)
                    ->placeholder('—'),

                TextColumn::make('eyebrow_en')
                    ->label('Eyebrow')
                    ->limit(30)
                    ->placeholder('—'),

                TextColumn::make('cta_title_en')
                    ->label('CTA')
                    ->limit(35)
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
