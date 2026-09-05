<?php

namespace App\Filament\Resources\HomeContactSections\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class HomeContactSectionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('title_en')
                    ->label('Title')
                    ->searchable()
                    ->weight('medium')
                    ->limit(60)
                    ->placeholder('—'),

                TextColumn::make('eyebrow_en')
                    ->label('Eyebrow')
                    ->limit(30)
                    ->placeholder('—'),

                TextColumn::make('cta_label_en')
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
