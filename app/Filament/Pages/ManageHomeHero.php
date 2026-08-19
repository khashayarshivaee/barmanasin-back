<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Models\HomeHero;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
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
use UnitEnum;

/**
 * @property-read Schema $form
 */
class ManageHomeHero extends Page
{
    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedPhoto;

    protected static ?string $navigationLabel = 'Home Hero';

    protected static string | UnitEnum | null $navigationGroup = 'Website';

    protected static ?int $navigationSort = 2;

    protected static ?string $title = 'Home Hero';

    protected string $view = 'filament.pages.manage-home-hero';

    /**
     * @var array<string, mixed> | null
     */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(
            $this->getRecord()?->attributesToArray(),
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
                            Section::make('Hero Content')
                                ->description(
                                    'Main bilingual content shown before and during the hero reveal.',
                                )
                                ->schema([
                                    TextInput::make('eyebrow_en')
                                        ->label('Eyebrow — English')
                                        ->placeholder('Mining • Engineering • Materials')
                                        ->maxLength(255),

                                    TextInput::make('eyebrow_fa')
                                        ->label('Eyebrow — Persian')
                                        ->placeholder('معدن • مهندسی • مواد')
                                        ->maxLength(255)
                                        ->extraInputAttributes([
                                            'dir' => 'rtl',
                                        ]),

                                    TextInput::make('title_en')
                                        ->label('Title — English')
                                        ->placeholder('Built from the ground up.')
                                        ->maxLength(255),

                                    TextInput::make('title_fa')
                                        ->label('Title — Persian')
                                        ->maxLength(255)
                                        ->extraInputAttributes([
                                            'dir' => 'rtl',
                                        ]),

                                    Textarea::make('description_en')
                                        ->label('Description — English')
                                        ->rows(4)
                                        ->maxLength(1000),

                                    Textarea::make('description_fa')
                                        ->label('Description — Persian')
                                        ->rows(4)
                                        ->maxLength(1000)
                                        ->extraInputAttributes([
                                            'dir' => 'rtl',
                                        ]),

                                    Textarea::make('fullscreen_caption_en')
                                        ->label('Fullscreen Caption — English')
                                        ->helperText(
                                            'Optional short statement displayed over the visual after it expands.',
                                        )
                                        ->rows(3)
                                        ->maxLength(500),

                                    Textarea::make('fullscreen_caption_fa')
                                        ->label('Fullscreen Caption — Persian')
                                        ->helperText(
                                            'متن کوتاه اختیاری که پس از باز شدن کامل تصویر نمایش داده می‌شود.',
                                        )
                                        ->rows(3)
                                        ->maxLength(500)
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
                                    'Control the primary action and whether the hero is published.',
                                )
                                ->schema([
                                    Toggle::make('is_active')
                                        ->label('Hero Active')
                                        ->helperText(
                                            'Turn this off to hide the hero from the public website.',
                                        )
                                        ->default(true),

                                    TextInput::make('cta_title_en')
                                        ->label('CTA Title — English')
                                        ->placeholder('Explore our work')
                                        ->maxLength(100),

                                    TextInput::make('cta_title_fa')
                                        ->label('CTA Title — Persian')
                                        ->maxLength(100)
                                        ->extraInputAttributes([
                                            'dir' => 'rtl',
                                        ]),

                                    TextInput::make('cta_path')
                                        ->label('CTA Path')
                                        ->placeholder('/projects')
                                        ->helperText(
                                            'Use an internal website path such as /projects.',
                                        )
                                        ->maxLength(255),
                                ])
                                ->columnSpan([
                                    'default' => 1,
                                    'xl' => 4,
                                ]),
                        ]),

                    Section::make('Hero Media')
                        ->description(
                            'Use a strong landscape image for desktop. Mobile image is optional.',
                        )
                        ->schema([
                            Grid::make([
                                'default' => 1,
                                'lg' => 2,
                            ])
                                ->schema([
                                    FileUpload::make('desktop_image')
                                        ->label('Desktop Image')
                                        ->image()
                                        ->imageEditor()
                                        ->disk('public')
                                        ->directory('home/hero')
                                        ->visibility('public')
                                        ->maxSize(8192)
                                        ->imagePreviewHeight('260')
                                        ->preventFilePathTampering()
                                        ->helperText(
                                            'Recommended: high-resolution landscape image. Maximum 8 MB.',
                                        ),

                                    FileUpload::make('mobile_image')
                                        ->label('Mobile Image')
                                        ->image()
                                        ->imageEditor()
                                        ->disk('public')
                                        ->directory('home/hero')
                                        ->visibility('public')
                                        ->maxSize(8192)
                                        ->imagePreviewHeight('260')
                                        ->preventFilePathTampering()
                                        ->helperText(
                                            'Optional. If empty, the desktop image will also be used on mobile.',
                                        ),
                                ]),

                            Grid::make([
                                'default' => 1,
                                'lg' => 2,
                            ])
                                ->schema([
                                    TextInput::make('image_alt_en')
                                        ->label('Image Alt — English')
                                        ->maxLength(255)
                                        ->helperText(
                                            'Describe the image briefly for accessibility and SEO.',
                                        ),

                                    TextInput::make('image_alt_fa')
                                        ->label('Image Alt — Persian')
                                        ->maxLength(255)
                                        ->extraInputAttributes([
                                            'dir' => 'rtl',
                                        ]),
                                ]),
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
            $record = new HomeHero();
        }

        $record->fill($data);
        $record->save();

        if ($record->wasRecentlyCreated) {
            $this->form
                ->record($record)
                ->saveRelationships();
        }

        Notification::make()
            ->success()
            ->title('Home Hero saved')
            ->body('Your hero content and media have been updated.')
            ->send();
    }

    public function getRecord(): ?HomeHero
    {
        return HomeHero::query()->first();
    }
}
