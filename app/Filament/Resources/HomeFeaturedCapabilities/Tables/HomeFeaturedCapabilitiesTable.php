<?php

namespace App\Filament\Resources\HomeFeaturedCapabilities\Tables;

use App\Models\Capability;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class HomeFeaturedCapabilitiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('sort_order')
                    ->label('#')
                    ->formatStateUsing(
                        fn ($state): string =>
                        str_pad(
                            (string) $state,
                            2,
                            '0',
                            STR_PAD_LEFT
                        )
                    )
                    ->sortable(),

                TextColumn::make('capability.title_en')
                    ->label('Capability')
                    ->searchable()
                    ->weight('medium')
                    ->limit(60)
                    ->description(
                        fn ($record): ?string =>
                        $record->capability?->short_description_en
                            ? Str::limit(
                            $record->capability->short_description_en,
                            80
                        )
                            : null
                    ),

                TextColumn::make('capability.status')
                    ->label('Status')
                    ->badge()
                    ->color(
                        fn (?string $state): string =>
                        match ($state) {
                            Capability::STATUS_PUBLISHED => 'success',
                            Capability::STATUS_ARCHIVED => 'gray',
                            default => 'warning',
                        }
                    )
                    ->formatStateUsing(
                        fn (?string $state): string =>
                        $state
                            ? ucfirst($state)
                            : 'Unknown'
                    ),

                ToggleColumn::make('is_active')
                    ->label('Featured'),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime('M j, Y — H:i')
                    ->sortable()
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

            ])

            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])

            ->defaultSort('sort_order')

            ->reorderable('sort_order')

            ->paginated(false);
    }
}
