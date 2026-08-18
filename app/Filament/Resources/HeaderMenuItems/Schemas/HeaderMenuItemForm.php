<?php

namespace App\Filament\Resources\HeaderMenuItems\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class HeaderMenuItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title_en')
                    ->label('English Title')
                    ->required()
                    ->maxLength(255),

                TextInput::make('title_fa')
                    ->label('Persian Title')
                    ->required()
                    ->maxLength(255),

                TextInput::make('path')
                    ->label('Path')
                    ->required()
                    ->maxLength(255)
                    ->helperText('Examples: /, /projects, /about'),

                TextInput::make('sort_order')
                    ->label('Order')
                    ->integer()
                    ->minValue(0)
                    ->default(0)
                    ->required(),

                Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),
            ]);
    }
}
