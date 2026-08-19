<?php

namespace App\Filament\Resources\HeaderMenuItems\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class HeaderMenuItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make([
                    'default' => 1,
                    'xl' => 12,
                ])
                    ->schema([
                        /*
                        |--------------------------------------------------------------------------
                        | Main Content
                        |--------------------------------------------------------------------------
                        */

                        Section::make('Menu Content')
                            ->description(
                                'Manage the labels and destination used in the website navigation.'
                            )
                            ->schema([
                                Grid::make([
                                    'default' => 1,
                                    'md' => 2,
                                ])
                                    ->schema([
                                        TextInput::make('title_en')
                                            ->label('English Title')
                                            ->placeholder('e.g. Products')
                                            ->required()
                                            ->maxLength(255)
                                            ->dehydrateStateUsing(
                                                fn (?string $state): ?string =>
                                                $state !== null
                                                    ? trim($state)
                                                    : null
                                            ),

                                        TextInput::make('title_fa')
                                            ->label('Persian Title')
                                            ->placeholder('مثلاً محصولات')
                                            ->required()
                                            ->maxLength(255)
                                            ->extraInputAttributes([
                                                'dir' => 'rtl',
                                            ])
                                            ->dehydrateStateUsing(
                                                fn (?string $state): ?string =>
                                                $state !== null
                                                    ? trim($state)
                                                    : null
                                            ),
                                    ]),

                                TextInput::make('path')
                                    ->label(
                                        fn (Get $get): string =>
                                        $get('type') === 'mega'
                                            ? 'Landing Page Path'
                                            : 'Destination Path'
                                    )
                                    ->placeholder('/project')
                                    ->helperText(
                                        fn (Get $get): string =>
                                        $get('type') === 'mega'
                                            ? 'The page associated with this mega menu item.'
                                            : 'The internal route opened when this menu item is selected.'
                                    )
                                    ->required()
                                    ->maxLength(255)
                                    ->extraInputAttributes([
                                        'dir' => 'ltr',
                                    ])
                                    ->dehydrateStateUsing(
                                        fn (?string $state): ?string =>
                                        $state !== null
                                            ? trim($state)
                                            : null
                                    ),
                            ])
                            ->columnSpan([
                                'default' => 1,
                                'xl' => 8,
                            ]),

                        /*
                        |--------------------------------------------------------------------------
                        | Sidebar Settings
                        |--------------------------------------------------------------------------
                        */

                        Section::make('Menu Settings')
                            ->description(
                                'Control the behavior, visibility and position of this item.'
                            )
                            ->schema([
                                Select::make('type')
                                    ->label('Menu Type')
                                    ->options([
                                        'link' => 'Normal Link',
                                        'mega' => 'Mega Menu',
                                    ])
                                    ->default('link')
                                    ->required()
                                    ->native(false)
                                    ->live()
                                    ->helperText(
                                        fn (Get $get): string =>
                                        $get('type') === 'mega'
                                            ? 'Displays a structured mega menu containing sections and links.'
                                            : 'Navigates directly to the destination page.'
                                    ),

                                Toggle::make('is_active')
                                    ->label('Visible on website')
                                    ->helperText(
                                        'Hide this item without deleting its configuration.'
                                    )
                                    ->default(true),

                                TextInput::make('sort_order')
                                    ->label('Menu Order')
                                    ->helperText(
                                        'Lower numbers appear earlier in the navigation.'
                                    )
                                    ->numeric()
                                    ->minValue(0)
                                    ->default(0)
                                    ->required(),
                            ])
                            ->columnSpan([
                                'default' => 1,
                                'xl' => 4,
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
