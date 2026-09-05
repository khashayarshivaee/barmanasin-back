<?php

namespace App\Filament\Resources\HomeFeaturedProjectsSections\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class HomeFeaturedProjectsSectionForm
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
                        'Content displayed above the Selected Works / Featured Projects section on the Home page.'
                    )
                    ->schema([

                        TextInput::make('eyebrow_en')
                            ->label('Eyebrow — English')
                            ->maxLength(255)
                            ->placeholder('SELECTED WORKS'),

                        TextInput::make('eyebrow_fa')
                            ->label('Eyebrow — Persian')
                            ->maxLength(255)
                            ->placeholder('پروژه‌های منتخب'),

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
                        'Optional link displayed below the Selected Works section.'
                    )
                    ->schema([

                        TextInput::make('cta_title_en')
                            ->label('CTA Title — English')
                            ->maxLength(255)
                            ->placeholder('View all projects'),

                        TextInput::make('cta_title_fa')
                            ->label('CTA Title — Persian')
                            ->maxLength(255)
                            ->placeholder('مشاهده همه پروژه‌ها'),

                        TextInput::make('cta_path')
                            ->label('CTA Path')
                            ->maxLength(255)
                            ->placeholder('/projects')
                            ->helperText(
                                'Internal frontend path. Example: /projects'
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
                                'Turn this off to hide the entire Selected Works section from the public API.'
                            ),

                    ])
                    ->columnSpanFull(),

            ]);
    }
}
