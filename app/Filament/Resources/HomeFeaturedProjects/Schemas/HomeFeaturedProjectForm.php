<?php

namespace App\Filament\Resources\HomeFeaturedProjects\Schemas;

use App\Models\HomeFeaturedProject;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class HomeFeaturedProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('Featured Project')
                    ->description(
                        'Select an existing project to feature in the Selected Works section on the Home page.'
                    )
                    ->schema([

                        Select::make('project_id')
                            ->label('Project')
                            ->relationship(
                                name: 'project',
                                titleAttribute: 'title_en',
                            )
                            ->searchable()
                            ->preload()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->columnSpanFull(),

                        TextInput::make('sort_order')
                            ->label('Sort Order')
                            ->numeric()
                            ->minValue(1)
                            ->default(
                                fn (): int =>
                                    (
                                        HomeFeaturedProject::query()
                                            ->max('sort_order') ?? 0
                                    ) + 1
                            )
                            ->required(),

                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->helperText(
                                'Only active featured records with a published project will appear on the website.'
                            ),

                    ])
                    ->columns(2)
                    ->columnSpanFull(),

            ]);
    }
}
