<?php

namespace App\Filament\Resources\HomeFeaturedProjects\Tables;

use App\Models\Project;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class HomeFeaturedProjectsTable
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

                ImageColumn::make('project.cover_image_path')
                    ->label('Cover')
                    ->disk('public')
                    ->visibility('public')
                    ->imageWidth(90)
                    ->imageHeight(56),

                TextColumn::make('project.title_en')
                    ->label('Project')
                    ->searchable()
                    ->weight('medium')
                    ->limit(55)
                    ->placeholder('—')
                    ->description(
                        fn ($record): ?string =>
                        $record->project?->location_en
                            ? Str::limit(
                            $record->project->location_en,
                            55
                        )
                            : null
                    ),

                TextColumn::make('project.category.name_en')
                    ->label('Category')
                    ->placeholder('—'),

                TextColumn::make('project.year')
                    ->label('Year')
                    ->placeholder('—'),

                TextColumn::make('project.status')
                    ->label('Project Status')
                    ->badge()
                    ->color(
                        fn (?string $state): string =>
                        match ($state) {
                            Project::STATUS_PUBLISHED => 'success',
                            Project::STATUS_ARCHIVED => 'gray',
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
