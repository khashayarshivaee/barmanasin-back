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

                /*
                |--------------------------------------------------------------------------
                | Sender Information
                |--------------------------------------------------------------------------
                */

                Section::make('Sender Information')
                    ->description(
                        'Contact details submitted by the website visitor.'
                    )
                    ->schema([

                        TextInput::make('name')
                            ->label('Name')
                            ->disabled(),

                        TextInput::make('company')
                            ->label('Company')
                            ->placeholder('—')
                            ->disabled(),

                        TextInput::make('email')
                            ->label('Email')
                            ->disabled(),

                        TextInput::make('phone')
                            ->label('Phone')
                            ->placeholder('—')
                            ->disabled(),

                    ])
                    ->columns(2)
                    ->columnSpanFull(),


                /*
                |--------------------------------------------------------------------------
                | Inquiry
                |--------------------------------------------------------------------------
                */

                Section::make('Inquiry')
                    ->description(
                        'Project context and message submitted through the Contact page.'
                    )
                    ->schema([

                        TextInput::make('project_type')
                            ->label('Project Type')
                            ->placeholder('—')
                            ->disabled()
                            ->columnSpanFull(),

                        Textarea::make('message')
                            ->label('Message')
                            ->rows(10)
                            ->disabled()
                            ->columnSpanFull(),

                    ])
                    ->columnSpanFull(),


                /*
                |--------------------------------------------------------------------------
                | Management
                |--------------------------------------------------------------------------
                */

                Section::make('Management')
                    ->description(
                        'Internal status used to track the inquiry.'
                    )
                    ->schema([

                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'new' => 'New',
                                'in_progress' => 'In Progress',
                                'resolved' => 'Resolved',
                            ])
                            ->required()
                            ->native(false)
                            ->helperText(
                                'Update the status as the inquiry is reviewed and handled.'
                            ),

                    ])
                    ->columnSpanFull(),

            ]);
    }
}
