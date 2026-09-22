<?php

namespace App\Filament\Resources\TeamProfiles\Schemas;

use App\Models\TeamProfile;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TeamProfileForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                /*
                |--------------------------------------------------------------------------
                | Team Member
                |--------------------------------------------------------------------------
                */

                Section::make('Team Member')
                    ->description(
                        'The Mail user who owns this public team profile.'
                    )
                    ->schema([

                        Select::make('user_id')
                            ->label('Mail User')
                            ->relationship(
                                name: 'user',
                                titleAttribute: 'name',
                            )
                            ->searchable()
                            ->preload()
                            ->disabled()
                            ->dehydrated(false),

                    ])
                    ->columnSpanFull(),


                /*
                |--------------------------------------------------------------------------
                | Profile Information
                |--------------------------------------------------------------------------
                */

                Section::make('Profile Information')
                    ->description(
                        'Bilingual information displayed on the public Our Team section.'
                    )
                    ->schema([

                        TextInput::make('name_en')
                            ->label('Name — English')
                            ->maxLength(120),

                        TextInput::make('name_fa')
                            ->label('Name — Persian')
                            ->maxLength(120),

                        TextInput::make('job_title_en')
                            ->label('Job Title — English')
                            ->maxLength(160),

                        TextInput::make('job_title_fa')
                            ->label('Job Title — Persian')
                            ->maxLength(160),

                        TextInput::make('department_en')
                            ->label('Department — English')
                            ->maxLength(160),

                        TextInput::make('department_fa')
                            ->label('Department — Persian')
                            ->maxLength(160),

                        Textarea::make('bio_en')
                            ->label('Biography — English')
                            ->rows(6)
                            ->maxLength(2000)
                            ->columnSpanFull(),

                        Textarea::make('bio_fa')
                            ->label('Biography — Persian')
                            ->rows(6)
                            ->maxLength(2000)
                            ->columnSpanFull(),

                    ])
                    ->columns(2)
                    ->columnSpanFull(),


                /*
                |--------------------------------------------------------------------------
                | Profile Photo
                |--------------------------------------------------------------------------
                */

                Section::make('Profile Photo')
                    ->description(
                        'The custom team photo uploaded by the user. If empty, the Mail avatar is used as a fallback.'
                    )
                    ->schema([

                        FileUpload::make('photo_path')
                            ->label('Team Photo')
                            ->image()
                            ->disk('public')
                            ->visibility('public')
                            ->imagePreviewHeight('240')
                            ->openable()
                            ->downloadable()
                            ->disabled()
                            ->dehydrated(false)
                            ->helperText(
                                'Photo changes are managed by the Mail user.'
                            ),

                    ])
                    ->columnSpanFull(),


                /*
                |--------------------------------------------------------------------------
                | Public Contact
                |--------------------------------------------------------------------------
                */

                Section::make('Public Contact')
                    ->description(
                        'Contact details provided by the team member for their public profile.'
                    )
                    ->schema([

                        TextInput::make('linkedin_url')
                            ->label('LinkedIn URL')
                            ->url()
                            ->maxLength(500),

                        TextInput::make('public_email')
                            ->label('Public Email')
                            ->email()
                            ->maxLength(255),

                        TextInput::make('public_phone')
                            ->label('Public Phone')
                            ->tel()
                            ->maxLength(50),

                        Toggle::make('show_email')
                            ->label('Show Email Publicly')
                            ->disabled()
                            ->dehydrated(false),

                        Toggle::make('show_phone')
                            ->label('Show Phone Publicly')
                            ->disabled()
                            ->dehydrated(false),

                    ])
                    ->columns(2)
                    ->columnSpanFull(),


                /*
                |--------------------------------------------------------------------------
                | Publication Request
                |--------------------------------------------------------------------------
                */

                Section::make('Publication Request')
                    ->description(
                        'Visibility and approval information. Approval is managed through dedicated admin actions.'
                    )
                    ->schema([

                        Toggle::make('is_public')
                            ->label('Requested Public Visibility')
                            ->disabled()
                            ->dehydrated(false),

                        Select::make('approval_status')
                            ->label('Approval Status')
                            ->options([
                                TeamProfile::STATUS_DRAFT =>
                                    'Draft',

                                TeamProfile::STATUS_PENDING =>
                                    'Pending Approval',

                                TeamProfile::STATUS_APPROVED =>
                                    'Approved',

                                TeamProfile::STATUS_REJECTED =>
                                    'Rejected',
                            ])
                            ->disabled()
                            ->dehydrated(false)
                            ->native(false),

                        DateTimePicker::make('submitted_at')
                            ->label('Submitted At')
                            ->seconds(false)
                            ->timezone('Asia/Tehran')
                            ->disabled()
                            ->dehydrated(false),

                        Select::make('approved_by')
                            ->label('Approved By')
                            ->relationship(
                                name: 'approvedBy',
                                titleAttribute: 'name',
                            )
                            ->searchable()
                            ->preload()
                            ->disabled()
                            ->dehydrated(false),

                        DateTimePicker::make('approved_at')
                            ->label('Approved At')
                            ->seconds(false)
                            ->timezone('Asia/Tehran')
                            ->disabled()
                            ->dehydrated(false),

                    ])
                    ->columns(2)
                    ->columnSpanFull(),

            ]);
    }
}
