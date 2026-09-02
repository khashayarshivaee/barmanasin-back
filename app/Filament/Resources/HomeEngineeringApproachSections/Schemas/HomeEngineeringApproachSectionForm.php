<?php

namespace App\Filament\Resources\HomeEngineeringApproachSections\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class HomeEngineeringApproachSectionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('Section Content')
                    ->description(
                        'Content displayed above the Engineering Approach section on the Home page.'
                    )
                    ->schema([

                        TextInput::make('eyebrow_en')
                            ->label('Eyebrow — English')
                            ->maxLength(255)
                            ->placeholder('OUR APPROACH'),


                        TextInput::make('eyebrow_fa')
                            ->label('Eyebrow — Persian')
                            ->maxLength(255)
                            ->placeholder('رویکرد ما'),


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
                            ->maxLength(2000),


                        Textarea::make('description_fa')
                            ->label('Description — Persian')
                            ->rows(5)
                            ->maxLength(2000),

                    ])
                    ->columns(2),


                Section::make('Visibility')
                    ->schema([

                        Toggle::make('is_active')
                            ->label('Section Active')
                            ->default(true)
                            ->helperText(
                                'Disable this to hide the Engineering Approach section from the public API.'
                            ),

                    ]),
            ]);
    }
}
