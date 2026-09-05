<?php

namespace App\Filament\Resources\HomeContactSections\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class HomeContactSectionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Section Content')
                    ->schema([
                        TextInput::make('eyebrow_en')
                            ->label('Eyebrow EN')
                            ->maxLength(255),

                        TextInput::make('eyebrow_fa')
                            ->label('Eyebrow FA')
                            ->maxLength(255),

                        TextInput::make('title_en')
                            ->label('Title EN')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('title_fa')
                            ->label('Title FA')
                            ->required()
                            ->maxLength(255),

                        Textarea::make('description_en')
                            ->label('Description EN')
                            ->rows(4),

                        Textarea::make('description_fa')
                            ->label('Description FA')
                            ->rows(4),

                        TextInput::make('cta_label_en')
                            ->label('CTA Label EN')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('cta_label_fa')
                            ->label('CTA Label FA')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('cta_path')
                            ->label('CTA Path')
                            ->required()
                            ->default('/contact')
                            ->maxLength(255),
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
