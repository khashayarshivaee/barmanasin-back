<?php

namespace App\Filament\Resources\SiteFooters\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SiteFooterForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                /*
                |--------------------------------------------------------------------------
                | Brand
                |--------------------------------------------------------------------------
                */

                Section::make('Brand')
                    ->description('Footer logo and brand description.')
                    ->schema([

                        FileUpload::make('logo_path')
                            ->label('Logo')
                            ->image()
                            ->disk('public')
                            ->directory('site/footer')
                            ->visibility('public')
                            ->imagePreviewHeight('120')
                            ->columnSpanFull(),

                        Textarea::make('description_en')
                            ->label('Description EN')
                            ->rows(5)
                            ->columnSpanFull(),

                        Textarea::make('description_fa')
                            ->label('Description FA')
                            ->rows(5)
                            ->columnSpanFull(),

                    ])
                    ->columns(2)
                    ->columnSpanFull(),


                /*
                |--------------------------------------------------------------------------
                | Contact Information
                |--------------------------------------------------------------------------
                */

                Section::make('Contact Information')
                    ->description('Public company contact details shown in the footer.')
                    ->schema([

                        Textarea::make('address_en')
                            ->label('Address EN')
                            ->rows(3)
                            ->columnSpanFull(),

                        Textarea::make('address_fa')
                            ->label('Address FA')
                            ->rows(3)
                            ->columnSpanFull(),

                        TextInput::make('phone')
                            ->label('Phone')
                            ->tel()
                            ->maxLength(255),

                        TextInput::make('fax')
                            ->label('Fax')
                            ->maxLength(255),

                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->maxLength(255),

                    ])
                    ->columns(2)
                    ->columnSpanFull(),


                /*
                |--------------------------------------------------------------------------
                | Copyright
                |--------------------------------------------------------------------------
                */

                Section::make('Copyright')
                    ->schema([

                        TextInput::make('copyright_en')
                            ->label('Copyright EN')
                            ->maxLength(255),

                        TextInput::make('copyright_fa')
                            ->label('Copyright FA')
                            ->maxLength(255),

                    ])
                    ->columns(2)
                    ->columnSpanFull(),


                /*
                |--------------------------------------------------------------------------
                | Visibility
                |--------------------------------------------------------------------------
                */

                Section::make('Visibility')
                    ->schema([

                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),

                    ])
                    ->columnSpanFull(),

            ]);
    }
}
