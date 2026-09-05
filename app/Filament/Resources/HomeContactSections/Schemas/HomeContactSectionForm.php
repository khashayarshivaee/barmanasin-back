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

                /*
                |--------------------------------------------------------------------------
                | Section Content
                |--------------------------------------------------------------------------
                */

                Section::make('Section Content')
                    ->description(
                        'Content displayed in the contact call-to-action section on the Home page.'
                    )
                    ->schema([

                        TextInput::make('eyebrow_en')
                            ->label('Eyebrow — English')
                            ->maxLength(255),

                        TextInput::make('eyebrow_fa')
                            ->label('Eyebrow — Persian')
                            ->maxLength(255),

                        TextInput::make('title_en')
                            ->label('Title — English')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('title_fa')
                            ->label('Title — Persian')
                            ->required()
                            ->maxLength(255),

                        Textarea::make('description_en')
                            ->label('Description — English')
                            ->rows(5)
                            ->maxLength(2000)
                            ->columnSpanFull(),

                        Textarea::make('description_fa')
                            ->label('Description — Persian')
                            ->rows(5)
                            ->maxLength(2000)
                            ->columnSpanFull(),

                    ])
                    ->columns(2)
                    ->columnSpanFull(),


                /*
                |--------------------------------------------------------------------------
                | Call to Action
                |--------------------------------------------------------------------------
                */

                Section::make('Call to Action')
                    ->description(
                        'Button text and destination used in the Home contact section.'
                    )
                    ->schema([

                        TextInput::make('cta_label_en')
                            ->label('CTA Label — English')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('cta_label_fa')
                            ->label('CTA Label — Persian')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('cta_path')
                            ->label('CTA Path')
                            ->required()
                            ->default('/contact')
                            ->maxLength(255)
                            ->placeholder('/contact')
                            ->helperText(
                                'Internal frontend path. Example: /contact'
                            )
                            ->columnSpanFull(),

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
                            ->label('Section Active')
                            ->default(true)
                            ->helperText(
                                'Turn this off to hide the Home contact section from the public API.'
                            ),

                    ])
                    ->columnSpanFull(),

            ]);
    }
}
