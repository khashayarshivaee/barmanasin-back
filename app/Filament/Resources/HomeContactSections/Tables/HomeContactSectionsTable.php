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
                TextColumn::make('eyebrow_en')
                    ->label('Eyebrow')
                    ->searchable(),

                TextColumn::make('title_en')
                    ->label('Title')
                    ->searchable()
                    ->wrap(),

                TextColumn::make('cta_label_en')
                    ->label('CTA'),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime()
                    ->sortable(),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
