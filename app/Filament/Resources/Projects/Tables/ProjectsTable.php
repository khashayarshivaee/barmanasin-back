<?php

namespace App\Filament\Resources\Projects\Tables;

use App\Models\Project;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ProjectsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                ImageColumn::make('cover_image_path')
                    ->label('Cover')
                    ->disk('public')
                    ->visibility('public')
                    ->imageWidth(90)
                    ->imageHeight(56),

                TextColumn::make('title_en')
                    ->label('Project')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->limit(60)
                    ->placeholder('—')
                    ->description(
                        fn (Project $record): ?string =>
                        $record->location_en
                            ? Str::limit(
                            $record->location_en,
                            60
                        )
                            : null
                    ),

                TextColumn::make('category.name_en')
                    ->label('Category')
                    ->sortable()
                    ->searchable()
                    ->placeholder('—'),

                TextColumn::make('year')
                    ->label('Year')
                    ->sortable()
                    ->placeholder('—'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(
                        fn (string $state): string =>
                        match ($state) {
                            Project::STATUS_PUBLISHED => 'success',
                            Project::STATUS_ARCHIVED => 'gray',
                            default => 'warning',
                        }
                    )
                    ->formatStateUsing(
                        fn (string $state): string =>
                        ucfirst($state)
                    ),

                TextColumn::make('published_at')
                    ->label('Published')
                    ->dateTime('M j, Y — H:i')
                    ->timezone('Asia/Tehran')
                    ->sortable()
                    ->placeholder('—'),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime('M j, Y — H:i')
                    ->timezone('Asia/Tehran')
                    ->sortable()
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

            ])

            ->filters([

                SelectFilter::make('project_category_id')
                    ->label('Category')
                    ->relationship(
                        'category',
                        'name_en'
                    )
                    ->searchable()
                    ->preload(),

                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        Project::STATUS_DRAFT => 'Draft',
                        Project::STATUS_PUBLISHED => 'Published',
                        Project::STATUS_ARCHIVED => 'Archived',
                    ]),

            ])

            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])

            ->defaultSort(
                'updated_at',
                'desc'
            );
    }
}
