<?php

namespace App\Filament\Resources\Capabilities\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FocusPointsRelationManager extends RelationManager
{
    protected static string $relationship =
        'focusPoints';

    protected static ?string $title =
        'Focus Points';


    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('Focus Point Content')
                    ->schema([

                        TextInput::make('title_en')
                            ->label('Title — English')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('title_fa')
                            ->label('Title — Persian')
                            ->required()
                            ->maxLength(255),

                    ])
                    ->columns(2)
                    ->columnSpanFull(),


                Section::make('Settings')
                    ->schema([

                        TextInput::make('sort_order')
                            ->label('Sort Order')
                            ->numeric()
                            ->minValue(1)
                            ->default(
                                fn (): int =>
                                    (
                                        $this
                                            ->getOwnerRecord()
                                            ->focusPoints()
                                            ->max('sort_order') ?? 0
                                    ) + 1
                            )
                            ->required(),

                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),

                    ])
                    ->columns(2)
                    ->columnSpanFull(),

            ]);
    }


    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title_en')

            ->columns([

                TextColumn::make('sort_order')
                    ->label('#')
                    ->formatStateUsing(
                        fn ($state): string =>
                        str_pad(
                            (string) $state,
                            2,
                            '0',
                            STR_PAD_LEFT
                        )
                    )
                    ->sortable(),

                TextColumn::make('title_en')
                    ->label('English')
                    ->searchable()
                    ->weight('medium')
                    ->wrap(),

                TextColumn::make('title_fa')
                    ->label('Persian')
                    ->searchable()
                    ->wrap(),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

            ])

            ->headerActions([
                CreateAction::make()
                    ->label('Add Focus Point'),
            ])

            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])

            ->defaultSort('sort_order')

            ->reorderable('sort_order')

            ->paginated(false);
    }
}
