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
                TextColumn::make('eyebrow_en')
                    ->label('Eyebrow')
                    ->placeholder('—'),

                TextColumn::make('title_en')
                    ->label('Title')
                    ->weight('medium')
                    ->wrap(),

                TextColumn::make('cta_title_en')
                    ->label('CTA')
                    ->placeholder('—'),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime('M j, Y H:i')
                    ->sortable(),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
