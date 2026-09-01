<?php

namespace App\Filament\Resources\ProjectCategories\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProjectCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Category Information')
                    ->description(
                        'Project categories are shared across the Projects section and Home featured projects.'
                    )
                    ->schema([
                        TextInput::make('name_en')
                            ->label('Name — English')
                            ->required()
                            ->maxLength(255)
                            ->autofocus(),

                        TextInput::make('name_fa')
                            ->label('Name — Persian')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->regex('/^[a-z0-9]+(?:-[a-z0-9]+)*$/')
                            ->helperText(
                                'Lowercase English letters, numbers and hyphens only. Example: mineral-processing'
                            ),

                        TextInput::make('sort_order')
                            ->label('Sort Order')
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->required(),

                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->helperText(
                                'Inactive categories remain stored but are hidden from public content.'
                            ),
                    ])
                    ->columns(2),
            ]);
    }
}
