<?php

namespace App\Filament\Resources\Projects\Schemas;

use App\Models\Project;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                /*
                |--------------------------------------------------------------------------
                | Project Information
                |--------------------------------------------------------------------------
                */

                Section::make('Project Information')
                    ->description(
                        'Core bilingual information used throughout the website.'
                    )
                    ->schema([

                        Select::make('project_category_id')
                            ->label('Category')
                            ->relationship(
                                name: 'category',
                                titleAttribute: 'name_en',
                            )
                            ->searchable()
                            ->preload()
                            ->required(),

                        TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->regex('/^[a-z0-9]+(?:-[a-z0-9]+)*$/')
                            ->helperText(
                                'Used in the public project URL. Example: sarcheshmeh-processing-plant'
                            ),

                        TextInput::make('title_en')
                            ->label('Title — English')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('title_fa')
                            ->label('Title — Persian')
                            ->required()
                            ->maxLength(255),

                        Textarea::make('short_description_en')
                            ->label('Short Description — English')
                            ->rows(5)
                            ->maxLength(1000)
                            ->columnSpanFull(),

                        Textarea::make('short_description_fa')
                            ->label('Short Description — Persian')
                            ->rows(5)
                            ->maxLength(1000)
                            ->columnSpanFull(),

                    ])
                    ->columns(2)
                    ->columnSpanFull(),


                /*
                |--------------------------------------------------------------------------
                | Project Details
                |--------------------------------------------------------------------------
                */

                Section::make('Project Details')
                    ->schema([

                        TextInput::make('location_en')
                            ->label('Location — English')
                            ->maxLength(255)
                            ->placeholder('Kerman, Iran'),

                        TextInput::make('location_fa')
                            ->label('Location — Persian')
                            ->maxLength(255)
                            ->placeholder('کرمان، ایران'),

                        TextInput::make('year')
                            ->label('Year')
                            ->numeric()
                            ->minValue(1900)
                            ->maxValue((int) date('Y') + 10)
                            ->placeholder((string) date('Y')),

                    ])
                    ->columns(2)
                    ->columnSpanFull(),


                /*
                |--------------------------------------------------------------------------
                | Media
                |--------------------------------------------------------------------------
                */

                Section::make('Media')
                    ->description(
                        'The main cover is required. A separate mobile image is optional when a different crop works better on smaller screens.'
                    )
                    ->schema([

                        FileUpload::make('cover_image_path')
                            ->label('Cover Image')
                            ->image()
                            ->disk('public')
                            ->directory('projects/covers')
                            ->visibility('public')
                            ->required()
                            ->maxSize(10240)
                            ->imagePreviewHeight('220')
                            ->openable()
                            ->downloadable()
                            ->preventFilePathTampering()
                            ->helperText(
                                'Main cinematic image. Maximum file size: 10 MB.'
                            ),

                        FileUpload::make('mobile_cover_image_path')
                            ->label('Mobile Cover Image')
                            ->image()
                            ->disk('public')
                            ->directory('projects/mobile-covers')
                            ->visibility('public')
                            ->maxSize(10240)
                            ->imagePreviewHeight('220')
                            ->openable()
                            ->downloadable()
                            ->preventFilePathTampering()
                            ->helperText(
                                'Optional. If empty, the frontend will use the main cover image.'
                            ),

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
                        'Control the publication status and timing of this project.'
                    )
                    ->schema([

                        Select::make('status')
                            ->label('Status')
                            ->options([
                                Project::STATUS_DRAFT => 'Draft',
                                Project::STATUS_PUBLISHED => 'Published',
                                Project::STATUS_ARCHIVED => 'Archived',
                            ])
                            ->default(Project::STATUS_DRAFT)
                            ->required()
                            ->native(false)
                            ->live(),

                        DateTimePicker::make('published_at')
                            ->label('Published At')
                            ->seconds(false)
                            ->timezone('Asia/Tehran')
                            ->required(
                                fn (Get $get): bool =>
                                    $get('status') === Project::STATUS_PUBLISHED
                            )
                            ->visible(
                                fn (Get $get): bool =>
                                    $get('status') === Project::STATUS_PUBLISHED
                            )
                            ->helperText(
                                'Required when the project is published. Time is shown in Tehran timezone.'
                            ),

                    ])
                    ->columns(2)
                    ->columnSpanFull(),

            ]);
    }
}
