<?php

namespace App\Filament\Resources\HomeImageShowcaseSections\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SlidesRelationManager extends RelationManager
{
    protected static string $relationship = 'slides';

    protected static ?string $title = 'Showcase Slides';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('image_path')
                    ->label('Image')
                    ->image()
                    ->disk('public')
                    ->directory('home/image-showcase')
                    ->visibility('public')
                    ->required(),

                TextInput::make('title_en')
                    ->label('Title EN')
                    ->required()
                    ->maxLength(255),

                TextInput::make('title_fa')
                    ->label('Title FA')
                    ->maxLength(255),

                Textarea::make('description_en')
                    ->label('Description EN')
                    ->rows(4),

                Textarea::make('description_fa')
                    ->label('Description FA')
                    ->rows(4),

                TextInput::make('sort_order')
                    ->label('Sort Order')
                    ->numeric()
                    ->default(
                        fn () =>
                            ($this->getOwnerRecord()
                                ->slides()
                                ->max('sort_order') ?? 0) + 1
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
            ->columns([
                TextColumn::make('sort_order')
                    ->label('#')
                    ->sortable(),

                ImageColumn::make('image_path')
                    ->label('Image')
                    ->disk('public')
                    ->square(),

                TextColumn::make('title_en')
                    ->label('Title')
                    ->searchable()
                    ->limit(50),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Add Slide')
                    ->visible(
                        fn (): bool =>
                            $this->getOwnerRecord()
                                ->slides()
                                ->count() < 5
                    ),
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
