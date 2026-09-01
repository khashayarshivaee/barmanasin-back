<?php

namespace App\Filament\Resources\HomeFeaturedCapabilities\Tables;

use App\Models\Capability;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class HomeFeaturedCapabilitiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sort_order')
                    ->label('#')
                    ->sortable(),

                TextColumn::make('capability.title_en')
                    ->label('Capability')
                    ->searchable()
                    ->weight('medium')
                    ->description(
                        fn ($record): ?string =>
                        $record->capability?->short_description_en
                    )
                    ->limit(80),

                TextColumn::make('capability.status')
                    ->label('Status')
                    ->badge()
                    ->color(
                        fn (?string $state): string => match ($state) {
                            Capability::STATUS_PUBLISHED => 'success',
                            Capability::STATUS_ARCHIVED => 'gray',
                            default => 'warning',
                        }
                    )
                    ->formatStateUsing(
                        fn (?string $state): string =>
                        $state ? ucfirst($state) : 'Unknown'
                    ),

                ToggleColumn::make('is_active')
                    ->label('Featured'),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime('M j, Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
