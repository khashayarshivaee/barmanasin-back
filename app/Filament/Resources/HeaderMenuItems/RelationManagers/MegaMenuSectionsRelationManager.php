<?php

namespace App\Filament\Resources\HeaderMenuItems\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class MegaMenuSectionsRelationManager extends RelationManager
{
    protected static string $relationship = 'megaMenuSections';

    protected static ?string $title = 'Mega Menu Sections';

    protected static ?string $recordTitleAttribute = 'title_en';

    public static function canViewForRecord(
        Model $ownerRecord,
        string $pageClass,
    ): bool {
        return $ownerRecord->type === 'mega';
    }

    public function form(Schema $schema): Schema
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
                        | Section Settings
                        |--------------------------------------------------------------------------
                        */

                        Section::make('Section Details')
                            ->description(
                                'Configure the title and visibility of this mega menu column.'
                            )
                            ->schema([
                                TextInput::make('title_en')
                                    ->label('English Title')
                                    ->placeholder('e.g. Products')
                                    ->maxLength(255),

                                TextInput::make('title_fa')
                                    ->label('Persian Title')
                                    ->placeholder('مثلاً محصولات')
                                    ->maxLength(255),

                                Toggle::make('is_active')
                                    ->label('Visible on website')
                                    ->helperText(
                                        'Turn this off to temporarily hide the entire section.'
                                    )
                                    ->default(true),

                                Hidden::make('sort_order')
                                    ->default(0),
                            ])
                            ->columns(1)
                            ->columnSpan([
                                'default' => 1,
                                'xl' => 4,
                            ]),

                        /*
                        |--------------------------------------------------------------------------
                        | Navigation Links
                        |--------------------------------------------------------------------------
                        */

                        Section::make('Navigation Links')
                            ->description(
                                'Create, arrange and manage the links displayed inside this section.'
                            )
                            ->schema([
                                Repeater::make('links')
                                    ->hiddenLabel()
                                    ->relationship()
                                    ->orderColumn('sort_order')
                                    ->schema([
                                        /*
                                        |--------------------------------------------------------------------------
                                        | Titles
                                        |--------------------------------------------------------------------------
                                        */

                                        Grid::make([
                                            'default' => 1,
                                            'xl' => 2,
                                        ])
                                            ->schema([
                                                TextInput::make('title_en')
                                                    ->label('English Title')
                                                    ->placeholder('e.g. Web Development')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->live(onBlur: true),

                                                TextInput::make('title_fa')
                                                    ->label('Persian Title')
                                                    ->placeholder('مثلاً طراحی وب')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->live(onBlur: true),
                                            ]),

                                        /*
                                        |--------------------------------------------------------------------------
                                        | Descriptions
                                        |--------------------------------------------------------------------------
                                        */

                                        Grid::make([
                                            'default' => 1,
                                            'xl' => 2,
                                        ])
                                            ->schema([
                                                Textarea::make('description_en')
                                                    ->label('English Description')
                                                    ->placeholder(
                                                        'Short description shown below the link title'
                                                    )
                                                    ->rows(3)
                                                    ->maxLength(255),

                                                Textarea::make('description_fa')
                                                    ->label('Persian Description')
                                                    ->placeholder(
                                                        'توضیح کوتاه برای نمایش زیر عنوان لینک'
                                                    )
                                                    ->rows(3)
                                                    ->maxLength(255),
                                            ]),

                                        /*
                                        |--------------------------------------------------------------------------
                                        | Destination
                                        |--------------------------------------------------------------------------
                                        */

                                        Grid::make([
                                            'default' => 1,
                                            'xl' => 2,
                                        ])
                                            ->schema([
                                                Select::make('link_type')
                                                    ->label('Link Type')
                                                    ->options([
                                                        'internal' => 'Internal Page',
                                                        'external' => 'External Website',
                                                    ])
                                                    ->default('internal')
                                                    ->required()
                                                    ->native(false)
                                                    ->live()
                                                    ->afterStateUpdated(
                                                        function (
                                                            Set $set,
                                                            ?string $state,
                                                        ): void {
                                                            if ($state === 'internal') {
                                                                $set(
                                                                    'open_in_new_tab',
                                                                    false,
                                                                );
                                                            }
                                                        }
                                                    ),

                                                TextInput::make('path')
                                                    ->label(
                                                        fn (Get $get): string =>
                                                        $get('link_type') === 'external'
                                                            ? 'External URL'
                                                            : 'Internal Path'
                                                    )
                                                    ->placeholder(
                                                        fn (Get $get): string =>
                                                        $get('link_type') === 'external'
                                                            ? 'https://example.com'
                                                            : '/project'
                                                    )
                                                    ->helperText(
                                                        fn (Get $get): string =>
                                                        $get('link_type') === 'external'
                                                            ? 'Enter the complete destination URL.'
                                                            : 'Enter the Angular route, for example /project.'
                                                    )
                                                    ->required()
                                                    ->maxLength(255),
                                            ]),

                                        /*
                                        |--------------------------------------------------------------------------
                                        | Appearance & Visibility
                                        |--------------------------------------------------------------------------
                                        */

                                        Grid::make([
                                            'default' => 1,
                                            'xl' => 2,
                                        ])
                                            ->schema([
                                                FileUpload::make('icon')
                                                    ->label('Menu Icon')
                                                    ->helperText(
                                                        'Optional square image displayed beside the link.'
                                                    )
                                                    ->image()
                                                    ->imageEditor()
                                                    ->imageEditorAspectRatioOptions([
                                                        '1:1',
                                                    ])
                                                    ->disk('public')
                                                    ->directory(
                                                        'header/mega-menu/icons'
                                                    )
                                                    ->visibility('public')
                                                    ->imagePreviewHeight('120')
                                                    ->maxSize(2048)
                                                    ->preventFilePathTampering(
                                                        allowFilePathUsing: fn (string $file): bool =>
                                                        str_starts_with($file, 'header/mega-menu/icons/'),
                                                    ),

                                                Grid::make(1)
                                                    ->schema([
                                                        Toggle::make('is_active')
                                                            ->label('Visible')
                                                            ->helperText(
                                                                'Show this link in the mega menu.'
                                                            )
                                                            ->default(true),

                                                        Toggle::make(
                                                            'open_in_new_tab'
                                                        )
                                                            ->label(
                                                                'Open in new tab'
                                                            )
                                                            ->helperText(
                                                                'Useful for links to external websites.'
                                                            )
                                                            ->default(false)
                                                            ->visible(
                                                                fn (Get $get): bool =>
                                                                    $get('link_type') === 'external'
                                                            ),
                                                    ]),
                                            ]),
                                    ])
                                    ->itemLabel(
                                        fn (array $state): string =>
                                            $state['title_en']
                                            ?? $state['title_fa']
                                            ?? 'New navigation link'
                                    )
                                    ->collapsible()
                                    ->collapsed()
                                    ->reorderable()
                                    ->defaultItems(0)
                                    ->addActionLabel('Add Navigation Link')
                                    ->extraAttributes([
                                        'style' => '
                                          max-height: calc(100vh - 320px);
                                          overflow-y: auto;
                                          overscroll-behavior: contain;
                                          padding-right: 6px;
                                           ',
                                    ])


                                    ->columnSpanFull(),
                            ])
                            ->columnSpan([
                                'default' => 1,
                                'xl' => 8,
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title_en')
            ->columns([
                TextColumn::make('title_en')
                    ->label('Section')
                    ->description(
                        fn (Model $record): string =>
                        $record->title_fa ?: 'No Persian title'
                    )
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                TextColumn::make('links_count')
                    ->label('Links')
                    ->counts('links')
                    ->badge()
                    ->color('gray')
                    ->alignCenter(),

                TextColumn::make('sort_order')
                    ->label('Order')
                    ->badge()
                    ->color('gray')
                    ->alignCenter(),

                ToggleColumn::make('is_active')
                    ->label('Visible')
                    ->alignCenter(),

                TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime('M j, Y H:i')
                    ->sortable()
                    ->toggleable(
                        isToggledHiddenByDefault: true,
                    ),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->emptyStateHeading('No mega menu sections yet')
            ->emptyStateDescription(
                'Create your first section and start adding navigation links.'
            )
            ->emptyStateIcon('heroicon-o-rectangle-stack')
            ->headerActions([
                CreateAction::make()
                    ->label('New Section')
                    ->icon('heroicon-m-plus')
                    ->modalHeading('Create Mega Menu Section')
                    ->modalDescription(
                        'Build a navigation section and manage all of its links.'
                    )
                    ->modalWidth(Width::Screen)
                    ->stickyModalHeader()
                    ->stickyModalFooter(),
            ])
            ->recordActions([
                EditAction::make()
                    ->label('Edit')
                    ->modalHeading('Edit Mega Menu Section')
                    ->modalWidth(Width::Screen)
                    ->stickyModalHeader()
                    ->stickyModalFooter(),

                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
