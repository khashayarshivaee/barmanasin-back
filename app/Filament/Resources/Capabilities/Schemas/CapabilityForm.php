<?php

namespace App\Filament\Resources\Capabilities\Schemas;

use App\Models\Capability;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class CapabilityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                /*
                |--------------------------------------------------------------------------
                | Capability Information
                |--------------------------------------------------------------------------
                */

                Section::make('Capability Information')
                    ->description(
                        'Core bilingual information used across the website.'
                    )
                    ->schema([

                        TextInput::make('title_en')
                            ->label('Title — English')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('title_fa')
                            ->label('Title — Persian')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->regex('/^[a-z0-9]+(?:-[a-z0-9]+)*$/')
                            ->helperText(
                                'Lowercase English letters, numbers and hyphens only.'
                            ),

                        TextInput::make('sort_order')
                            ->label('Default Sort Order')
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->required(),

                        Textarea::make('short_description_en')
                            ->label('Short Description — English')
                            ->rows(5)
                            ->maxLength(1500)
                            ->columnSpanFull(),

                        Textarea::make('short_description_fa')
                            ->label('Short Description — Persian')
                            ->rows(5)
                            ->maxLength(1500)
                            ->columnSpanFull(),

                    ])
                    ->columns(2)
                    ->columnSpanFull(),


                /*
                |--------------------------------------------------------------------------
                | Publishing
                |--------------------------------------------------------------------------
                */

                Section::make('Publishing')
                    ->description(
                        'Control publication status and public visibility.'
                    )
                    ->schema([

                        Select::make('status')
                            ->label('Status')
                            ->options([
                                Capability::STATUS_DRAFT => 'Draft',
                                Capability::STATUS_PUBLISHED => 'Published',
                                Capability::STATUS_ARCHIVED => 'Archived',
                            ])
                            ->default(Capability::STATUS_DRAFT)
                            ->required()
                            ->native(false)
                            ->live(),

                        DateTimePicker::make('published_at')
                            ->label('Published At')
                            ->seconds(false)
                            ->timezone('Asia/Tehran')
                            ->required(
                                fn (Get $get): bool =>
                                    $get('status') === Capability::STATUS_PUBLISHED
                            )
                            ->visible(
                                fn (Get $get): bool =>
                                    $get('status') === Capability::STATUS_PUBLISHED
                            )
                            ->helperText(
                                'Time is shown in Tehran timezone.'
                            ),

                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->helperText(
                                'Inactive capabilities will not appear in public API responses.'
                            )
                            ->columnSpanFull(),

                    ])
                    ->columns(2)
                    ->columnSpanFull(),

            ]);
    }
}
