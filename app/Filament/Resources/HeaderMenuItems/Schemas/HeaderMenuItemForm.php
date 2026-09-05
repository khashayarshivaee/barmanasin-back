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
                        | Menu Content
                        |--------------------------------------------------------------------------
                        */

                        Section::make('Menu Content')
                            ->description(
                                'Manage the bilingual labels and destination used in the website navigation.'
                            )
                            ->schema([

                                Grid::make([
                                    'default' => 1,
                                    'md' => 2,
                                ])
                                    ->schema([

                                        TextInput::make('title_en')
                                            ->label('Title — English')
                                            ->placeholder('e.g. Products')
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

                                        TextInput::make('title_fa')
                                            ->label('Title — Persian')
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
                                    ->placeholder('/projects')
                                    ->helperText(
                                        fn (Get $get): string =>
                                        $get('type') === 'mega'
                                            ? 'The frontend page associated with this mega menu item.'
                                            : 'The internal frontend route opened when this menu item is selected.'
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
                                    )
                                    ->columnSpanFull(),

                            ])
                            ->columnSpan([
                                'default' => 1,
                                'xl' => 8,
                            ]),


                        /*
                        |--------------------------------------------------------------------------
                        | Menu Settings
                        |--------------------------------------------------------------------------
                        */

                        Section::make('Menu Settings')
                            ->description(
                                'Control the behavior, visibility and position of this navigation item.'
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
                                            : 'Navigates directly to the destination path.'
                                    ),

                                TextInput::make('sort_order')
                                    ->label('Menu Order')
                                    ->numeric()
                                    ->minValue(0)
                                    ->default(0)
                                    ->required()
                                    ->helperText(
                                        'Lower numbers appear earlier in the website navigation.'
                                    ),

                                Toggle::make('is_active')
                                    ->label('Visible on Website')
                                    ->default(true)
                                    ->helperText(
                                        'Hide this item without deleting its configuration.'
                                    ),

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
