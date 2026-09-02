<?php

namespace App\Filament\Resources\HomeEngineeringApproachSections\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StepsRelationManager extends RelationManager
{
    protected static string $relationship = 'steps';


    protected static ?string $title = 'Process Steps';


    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([

                TextInput::make('title_en')
                    ->label('Title — English')
                    ->required()
                    ->maxLength(255),


                TextInput::make('title_fa')
                    ->label('Title — Persian')
                    ->required()
                    ->maxLength(255),


                Textarea::make('description_en')
                    ->label('Description — English')
                    ->rows(4),


                Textarea::make('description_fa')
                    ->label('Description — Persian')
                    ->rows(4),


                TextInput::make('sort_order')
                    ->label('Sort Order')
                    ->numeric()
                    ->default(
                        fn () => (
                                $this->getOwnerRecord()
                                    ->steps()
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


                TextColumn::make('title_en')
                    ->label('Step')
                    ->weight('medium')
                    ->wrap(),


                TextColumn::make('description_en')
                    ->label('Description')
                    ->limit(80)
                    ->wrap()
                    ->toggleable(),


                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

            ])

            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->paginated(false)

            ->headerActions([
                CreateAction::make()
                    ->label('Add Step'),
            ])

            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
