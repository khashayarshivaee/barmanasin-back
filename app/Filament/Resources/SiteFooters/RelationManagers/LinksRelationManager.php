<?php

namespace App\Filament\Resources\SiteFooters\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LinksRelationManager extends RelationManager
{
    protected static string $relationship = 'links';

    protected static ?string $title = 'Footer Links';


    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([

                Select::make('group')
                    ->label('Group')
                    ->options([
                        'services' => 'Services',
                        'about' => 'About',
                        'social' => 'Social',
                    ])
                    ->required()
                    ->native(false),

                TextInput::make('title_en')
                    ->label('Title EN')
                    ->required()
                    ->maxLength(255),

                TextInput::make('title_fa')
                    ->label('Title FA')
                    ->required()
                    ->maxLength(255),

                TextInput::make('url')
                    ->label('URL / Path')
                    ->required()
                    ->maxLength(255),

                TextInput::make('sort_order')
                    ->label('Sort Order')
                    ->numeric()
                    ->default(
                        fn (): int =>
                            (
                                $this
                                    ->getOwnerRecord()
                                    ->links()
                                    ->max('sort_order') ?? 0
                            ) + 1
                    )
                    ->required(),

                Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),

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

                TextColumn::make('group')
                    ->label('Group')
                    ->badge()
                    ->formatStateUsing(
                        fn (string $state): string =>
                        match ($state) {
                            'services' => 'Services',
                            'about' => 'About',
                            'social' => 'Social',
                            default => ucfirst($state),
                        }
                    ),

                TextColumn::make('title_en')
                    ->label('Title')
                    ->searchable(),

                TextColumn::make('url')
                    ->label('URL')
                    ->limit(40),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

            ])

            ->headerActions([
                CreateAction::make()
                    ->label('Add Footer Link'),
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
