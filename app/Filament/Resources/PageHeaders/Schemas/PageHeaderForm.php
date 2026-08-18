<?php

namespace App\Filament\Resources\PageHeaders\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PageHeaderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('page_key')
                    ->label('Page')
                    ->options([
                        'home' => 'Home',
                        'projects' => 'Projects',
                        'about' => 'About',
                        'contact' => 'Contact',
                    ])
                    ->required()
                    ->unique(ignoreRecord: true),

                TextInput::make('title_en')
                    ->label('English Title')
                    ->required()
                    ->maxLength(255),

                TextInput::make('title_fa')
                    ->label('Persian Title')
                    ->required()
                    ->maxLength(255),
            ]);
    }
}
