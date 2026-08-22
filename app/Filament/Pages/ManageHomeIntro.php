<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Models\HomeIntro;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Arr;
use UnitEnum;

/**
 * @property-read Schema $form
 */
class ManageHomeIntro extends Page
{
    protected static string | BackedEnum | null $navigationIcon =
        Heroicon::OutlinedBuildingOffice2;

    protected static ?string $navigationLabel = 'Home Intro';

    protected static string | UnitEnum | null $navigationGroup =
        'Website';

    protected static ?int $navigationSort = 3;

    protected static ?string $title = 'Home Intro';

    protected string $view =
        'filament.pages.manage-home-intro';

    /** @var array<string, mixed> | null */
    public ?array $data = [];

    public function mount(): void
    {
        $record = $this->getRecord();

        $record->load('facts');

        $this->form->fill(
            $record->attributesToArray(),
        );
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Form::make([
                    Grid::make([
                        'default' => 1,
                        'xl' => 12,
                    ])
                        ->schema([
                            Section::make('Intro Content')
                                ->description(
                                    'Main bilingual content shown directly below the home hero.',
                                )
                                ->schema([
                                    TextInput::make('eyebrow_en')
                                        ->label('Eyebrow — English')
                                        ->placeholder('WHO WE ARE')
                                        ->maxLength(255),

                                    TextInput::make('eyebrow_fa')
                                        ->label('Eyebrow — Persian')
                                        ->placeholder('درباره ما')
                                        ->maxLength(255)
                                        ->extraInputAttributes([
                                            'dir' => 'rtl',
                                        ]),

                                    Textarea::make('title_en')
                                        ->label('Title — English')
                                        ->rows(3)
                                        ->placeholder(
                                            'Engineering resources into lasting value.',
                                        )
                                        ->maxLength(1000),

                                    Textarea::make('title_fa')
                                        ->label('Title — Persian')
                                        ->rows(3)
                                        ->maxLength(1000)
                                        ->extraInputAttributes([
                                            'dir' => 'rtl',
                                        ]),

                                    Textarea::make('description_en')
                                        ->label('Description — English')
                                        ->rows(5)
                                        ->maxLength(2000),

                                    Textarea::make('description_fa')
                                        ->label('Description — Persian')
                                        ->rows(5)
                                        ->maxLength(2000)
                                        ->extraInputAttributes([
                                            'dir' => 'rtl',
                                        ]),
                                ])
                                ->columns(2)
                                ->columnSpan([
                                    'default' => 1,
                                    'xl' => 8,
                                ]),

                            Section::make('Action & Status')
                                ->description(
                                    'Control the optional link and visibility of this section.',
                                )
                                ->schema([
                                    Toggle::make('is_active')
                                        ->label('Section Active')
                                        ->default(true),

                                    TextInput::make('cta_title_en')
                                        ->label('CTA Title — English')
                                        ->placeholder('About Barman Asin')
                                        ->maxLength(100),

                                    TextInput::make('cta_title_fa')
                                        ->label('CTA Title — Persian')
                                        ->maxLength(100)
                                        ->extraInputAttributes([
                                            'dir' => 'rtl',
                                        ]),

                                    TextInput::make('cta_path')
                                        ->label('CTA Path')
                                        ->placeholder('/about')
                                        ->helperText(
                                            'Use an internal path such as /about or /projects.',
                                        )
                                        ->maxLength(255),
                                ])
                                ->columnSpan([
                                    'default' => 1,
                                    'xl' => 4,
                                ]),
                        ]),

                    Section::make('Facts & Statistics')
                        ->description(
                            'Add the key numbers shown below the intro. Drag items to control their order.',
                        )
                        ->schema([
                            Repeater::make('facts')
                                ->relationship()
                                ->orderColumn('sort_order')
                                ->schema([
                                    Grid::make([
                                        'default' => 1,
                                        'md' => 2,
                                        'xl' => 4,
                                    ])
                                        ->schema([
                                            TextInput::make('value')
                                                ->label('Value')
                                                ->placeholder('25')
                                                ->required()
                                                ->maxLength(100),

                                            TextInput::make('suffix_en')
                                                ->label('Suffix — English')
                                                ->placeholder('+')
                                                ->maxLength(50),

                                            TextInput::make('suffix_fa')
                                                ->label('Suffix — Persian')
                                                ->placeholder('+')
                                                ->maxLength(50)
                                                ->extraInputAttributes([
                                                    'dir' => 'rtl',
                                                ]),

                                            Toggle::make('is_active')
                                                ->label('Active')
                                                ->default(true),
                                        ]),

                                    Grid::make([
                                        'default' => 1,
                                        'md' => 2,
                                    ])
                                        ->schema([
                                            TextInput::make('label_en')
                                                ->label('Label — English')
                                                ->placeholder('Projects')
                                                ->maxLength(255),

                                            TextInput::make('label_fa')
                                                ->label('Label — Persian')
                                                ->placeholder('پروژه')
                                                ->maxLength(255)
                                                ->extraInputAttributes([
                                                    'dir' => 'rtl',
                                                ]),
                                        ]),
                                ])
                                ->defaultItems(0)
                                ->addActionLabel('Add Fact')
                                ->reorderable()
                                ->collapsible()
                                ->itemLabel(
                                    fn (array $state): ?string =>
                                    filled($state['label_en'] ?? null)
                                        ? ($state['value'] ?? '') .
                                        ' — ' .
                                        $state['label_en']
                                        : ($state['value'] ?? 'New Fact'),
                                ),
                        ]),
                ])
                    ->livewireSubmitHandler('save')
                    ->footer([
                        Actions::make([
                            Action::make('save')
                                ->label('Save Changes')
                                ->icon(Heroicon::OutlinedCheck)
                                ->submit('save')
                                ->keyBindings(['mod+s']),
                        ]),
                    ]),
            ])
            ->record($this->getRecord())
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $record = $this->getRecord();

        if (! $record) {
            $record = new HomeIntro();
        }

        $record->fill(
            Arr::except($data, ['facts']),
        );

        $record->save();

        $this->form
            ->record($record)
            ->saveRelationships();

        Notification::make()
            ->success()
            ->title('Home Intro saved')
            ->body(
                'The intro content and statistics have been updated.',
            )
            ->send();
    }

    public function getRecord(): HomeIntro
    {
        return HomeIntro::query()->firstOrCreate(
            [],
            [
                'is_active' => true,
            ],
        );
    }
}
