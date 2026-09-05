<?php

namespace App\Filament\Resources\HeaderMenuItems\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class HeaderMenuItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('title_en')
                    ->label('Menu Item')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->limit(45)
                    ->placeholder('—'),

                TextColumn::make('title_fa')
                    ->label('Persian')
                    ->searchable()
                    ->limit(45)
                    ->placeholder('—'),

                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(
                        fn (string $state): string =>
                        match ($state) {
                            'mega' => 'Mega Menu',
                            default => 'Normal Link',
                        }
                    )
                    ->color(
                        fn (string $state): string =>
                        match ($state) {
                            'mega' => 'info',
                            default => 'gray',
                        }
                    ),

                TextColumn::make('path')
                    ->label('Destination')
                    ->limit(45)
                    ->placeholder('—')
                    ->tooltip(
                        fn ($state): ?string =>
                        $state ?: null
                    ),

                TextColumn::make('mega_menu_sections_count')
                    ->label('Sections')
                    ->counts('megaMenuSections')
                    ->badge()
                    ->color('gray')
                    ->alignCenter(),

                TextColumn::make('sort_order')
                    ->label('Order')
                    ->formatStateUsing(
                        fn ($state): string =>
                        str_pad(
                            (string) $state,
                            2,
                            '0',
                            STR_PAD_LEFT
                        )
                    )
                    ->sortable()
                    ->alignCenter(),

                IconColumn::make('is_active')
                    ->label('Visible')
                    ->boolean()
                    ->alignCenter(),

                TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime('M j, Y — H:i')
                    ->sortable()
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

            ])

            ->recordActions([
                EditAction::make()
                    ->label('Edit'),
            ])

            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])

            ->defaultSort('sort_order')

            ->reorderable('sort_order')

            ->paginated(false)

            ->stackedOnMobile();
    }
}
