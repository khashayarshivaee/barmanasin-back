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
                    ->weight('medium'),

                TextColumn::make('title_fa')
                    ->label('Persian')
                    ->searchable(),

                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(
                        fn (string $state): string => match ($state) {
                            'mega' => 'Mega Menu',
                            default => 'Normal Link',
                        }
                    )
                    ->color(
                        fn (string $state): string => match ($state) {
                            'mega' => 'info',
                            default => 'gray',
                        }
                    ),

                TextColumn::make('path')
                    ->label('Destination')
                    ->placeholder('—'),

                TextColumn::make('mega_menu_sections_count')
                    ->label('Sections')
                    ->counts('megaMenuSections')
                    ->badge()
                    ->color('gray')
                    ->alignCenter(),

                TextColumn::make('sort_order')
                    ->label('Order')
                    ->sortable()
                    ->alignCenter(),

                IconColumn::make('is_active')
                    ->label('Visible')
                    ->boolean()
                    ->alignCenter(),

                TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime('M j, Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order')
            ->stackedOnMobile()
            ->recordActions([
                EditAction::make()
                    ->label('Edit'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
