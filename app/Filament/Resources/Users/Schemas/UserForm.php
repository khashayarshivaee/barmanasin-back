<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('User Identity')
                    ->description(
                        'Basic account information for this user.'
                    )
                    ->schema([

                        TextInput::make('name')
                            ->label('Full Name')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('mailbox_username')
                            ->label('Email Username')
                            ->placeholder('morteza')
                            ->suffix('@barmanasin.com')
                            ->helperText(
                                'Enter only the username. The company email address will be generated automatically.'
                            )
                            ->required(
                                fn (string $operation): bool =>
                                    $operation === 'create'
                            )
                            ->visible(
                                fn (string $operation): bool =>
                                    $operation === 'create'
                            )
                            ->regex(
                                '/^[a-z0-9][a-z0-9._-]{1,63}$/'
                            )
                            ->maxLength(64),

                        TextInput::make('email')
                            ->label('Account Email')
                            ->disabled()
                            ->dehydrated(false)
                            ->visible(
                                fn (string $operation): bool =>
                                    $operation === 'edit'
                            ),

                    ])
                    ->columns(2)
                    ->columnSpanFull(),


                Section::make('Role & Access')
                    ->description(
                        'Control what the user can access inside Barmanasin.'
                    )
                    ->schema([

                        Select::make('role')
                            ->label('Role')
                            ->options([
                                User::ROLE_MAIL_USER => 'Mail User',
                                User::ROLE_SUPER_ADMIN => 'Super Admin',
                            ])
                            ->default(User::ROLE_MAIL_USER)
                            ->required()
                            ->native(false),

                        Toggle::make('is_active')
                            ->label('Account Active')
                            ->default(true)
                            ->helperText(
                                'Inactive users cannot sign in.'
                            ),

                    ])
                    ->columns(2)
                    ->columnSpanFull(),


                Section::make('Mailbox')
                    ->description(
                        'Mailbox configuration for the company email account.'
                    )
                    ->schema([

                        Toggle::make('mailbox_enabled')
                            ->label('Enable Mailbox')
                            ->default(true)
                            ->helperText(
                                'A real mailbox will be provisioned when the mail server integration is enabled.'
                            ),

                        TextInput::make('mailbox_quota_mb')
                            ->label('Mailbox Quota')
                            ->numeric()
                            ->default(5120)
                            ->minValue(512)
                            ->maxValue(51200)
                            ->suffix('MB')
                            ->required(),

                        TextInput::make('mailbox_address')
                            ->label('Mailbox Address')
                            ->disabled()
                            ->dehydrated(false)
                            ->placeholder('Created automatically')
                            ->visible(
                                fn (string $operation): bool =>
                                    $operation === 'edit'
                            ),

                    ])
                    ->columns(2)
                    ->columnSpanFull(),

            ]);
    }
}
