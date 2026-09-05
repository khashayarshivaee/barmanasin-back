<?php

namespace App\Filament\Resources\HomeFeaturedCapabilities\Schemas;

use App\Models\HomeFeaturedCapability;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class HomeFeaturedCapabilityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('Featured Capability')
                    ->description(
                        'Select an existing capability to display in the Home Capabilities section.'
                    )
                    ->schema([

                        Select::make('capability_id')
                            ->label('Capability')
                            ->relationship(
                                name: 'capability',
                                titleAttribute: 'title_en',
                            )
                            ->searchable()
                            ->preload()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->columnSpanFull(),

                        TextInput::make('sort_order')
                            ->label('Home Sort Order')
                            ->numeric()
                            ->minValue(1)
                            ->default(
                                fn (): int =>
                                    (
                                        HomeFeaturedCapability::query()
                                            ->max('sort_order') ?? 0
                                    ) + 1
                            )
                            ->required(),

                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->helperText(
                                'Only active featured capabilities with published capability content will appear on the website.'
                            ),

                    ])
                    ->columns(2)
                    ->columnSpanFull(),

            ]);
    }
}
