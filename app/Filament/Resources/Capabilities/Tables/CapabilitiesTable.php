<?php

namespace App\Filament\Resources\Capabilities\Tables;

use App\Models\Capability;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CapabilitiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sort_order')
                    ->label('#')
                    ->sortable(),

                TextColumn::make('title_en')
                    ->label('Capability')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->description(
                        fn (Capability $record): ?string =>
                        $record->short_description_en
                    )
                    ->limit(80),

                TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->copyable(),

                TextColumn::make('focus_points_count')
                    ->label('Focus Points')
                    ->counts('focusPoints'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(
                        fn (string $state): string => match ($state) {
                            Capability::STATUS_PUBLISHED => 'success',
                            Capability::STATUS_ARCHIVED => 'gray',
                            default => 'warning',
                        }
                    )
                    ->formatStateUsing(
                        fn (string $state): string => ucfirst($state)
                    ),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

                TextColumn::make('published_at')
                    ->label('Published')
                    ->dateTime('M j, Y H:i')
                    ->timezone('Asia/Tehran')
                    ->placeholder('—')
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime('M j, Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        Capability::STATUS_DRAFT => 'Draft',
                        Capability::STATUS_PUBLISHED => 'Published',
                        Capability::STATUS_ARCHIVED => 'Archived',
                    ]),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
