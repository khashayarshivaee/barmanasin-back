<?php

namespace App\Filament\Resources\ProjectCategories\Tables;

use App\Models\ProjectCategory;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class ProjectCategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sort_order')
                    ->label('#')
                    ->sortable(),

                TextColumn::make('name_en')
                    ->label('English')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                TextColumn::make('name_fa')
                    ->label('Persian')
                    ->searchable(),

                TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->copyable(),

                ToggleColumn::make('is_active')
                    ->label('Active'),

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

                DeleteAction::make()
                    ->visible(
                        fn (ProjectCategory $record): bool => $record
                            ->projects()
                            ->doesntExist()
                    ),
            ]);
    }
}
