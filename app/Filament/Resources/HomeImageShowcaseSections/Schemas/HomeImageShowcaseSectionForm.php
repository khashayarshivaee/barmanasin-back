<?php

namespace App\Filament\Resources\HomeImageShowcaseSections\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class HomeImageShowcaseSectionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Content')
                    ->schema([
                        TextInput::make('eyebrow_en')
                            ->label('Eyebrow EN')
                            ->maxLength(255),

                        TextInput::make('eyebrow_fa')
                            ->label('Eyebrow FA')
                            ->maxLength(255),

                        TextInput::make('title_en')
                            ->label('Title EN')
                            ->maxLength(255),

                        TextInput::make('title_fa')
                            ->label('Title FA')
                            ->maxLength(255),

                        Textarea::make('description_en')
                            ->label('Description EN')
                            ->rows(4),

                        Textarea::make('description_fa')
                            ->label('Description FA')
                            ->rows(4),
                    ])
                    ->columns(2),

                Section::make('Visibility')
                    ->schema([
                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),
                    ]),
            ]);
    }
}
