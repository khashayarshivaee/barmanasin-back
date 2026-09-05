<?php

namespace App\Filament\Resources\ContactInquiries\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ContactInquiryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('Sender Information')
                    ->schema([

                        TextInput::make('name')
                            ->label('Name')
                            ->disabled(),

                        TextInput::make('company')
                            ->label('Company')
                            ->disabled(),

                        TextInput::make('email')
                            ->label('Email')
                            ->disabled(),

                        TextInput::make('phone')
                            ->label('Phone')
                            ->disabled(),

                    ])
                    ->columns(2),


                Section::make('Inquiry')
                    ->schema([

                        TextInput::make('project_type')
                            ->label('Project Type')
                            ->disabled(),

                        Textarea::make('message')
                            ->label('Message')
                            ->rows(8)
                            ->disabled()
                            ->columnSpanFull(),

                    ]),


                Section::make('Management')
                    ->schema([

                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'new' => 'New',
                                'in_progress' => 'In Progress',
                                'resolved' => 'Resolved',
                            ])
                            ->required()
                            ->native(false),

                    ])
                    ->columns(1),

            ]);
    }
}
