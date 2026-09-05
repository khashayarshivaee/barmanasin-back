<?php

namespace App\Filament\Resources\HomeCapabilitiesSections\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class HomeCapabilitiesSectionForm
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
                        'Content displayed above the Capabilities section on the Home page.'
                    )
                    ->schema([

                        TextInput::make('eyebrow_en')
                            ->label('Eyebrow — English')
                            ->maxLength(255)
                            ->placeholder('CAPABILITIES'),

                        TextInput::make('eyebrow_fa')
                            ->label('Eyebrow — Persian')
                            ->maxLength(255)
                            ->placeholder('توانمندی‌ها'),

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
                            ->maxLength(1500)
                            ->columnSpanFull(),

                        Textarea::make('description_fa')
                            ->label('Description — Persian')
                            ->rows(5)
                            ->maxLength(1500)
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
                        'Optional link displayed within the Capabilities section.'
                    )
                    ->schema([

                        TextInput::make('cta_title_en')
                            ->label('CTA Title — English')
                            ->maxLength(255)
                            ->placeholder('Explore capabilities'),

                        TextInput::make('cta_title_fa')
                            ->label('CTA Title — Persian')
                            ->maxLength(255)
                            ->placeholder('مشاهده توانمندی‌ها'),

                        TextInput::make('cta_path')
                            ->label('CTA Path')
                            ->maxLength(255)
                            ->placeholder('/capabilities')
                            ->helperText(
                                'Internal frontend path. Example: /capabilities'
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
                                'Turn this off to hide the entire Capabilities section from the public API.'
                            ),

                    ])
                    ->columnSpanFull(),

            ]);
    }
}
