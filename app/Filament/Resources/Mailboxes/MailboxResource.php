<?php

namespace App\Filament\Resources\Mailboxes;

use App\Filament\Resources\Mailboxes\Pages;
use App\Models\Mailbox;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;

class MailboxResource extends Resource
{
    protected static ?string $model = Mailbox::class;


    protected static string|\BackedEnum|null $navigationIcon =
        'heroicon-o-envelope';


    protected static string|\UnitEnum|null $navigationGroup =
        'Mail Management';


    protected static ?string $navigationLabel =
        'Mailboxes';


    protected static ?int $navigationSort = 10;


    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([

                Forms\Components\TextInput::make('address')
                    ->label('Email Address')
                    ->disabled(),

                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'active' => 'Active',
                        'suspended' => 'Suspended',
                        'disabled' => 'Disabled',
                    ])
                    ->required(),

                Forms\Components\TextInput::make('quota_mb')
                    ->label('Quota (MB)')
                    ->numeric()
                    ->required(),

            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\TextColumn::make('address')
                    ->label('Mailbox')
                    ->searchable()
                    ->sortable(),


                Tables\Columns\TextColumn::make('user.name')
                    ->label('Owner')
                    ->searchable(),


                Tables\Columns\TextColumn::make('provider')
                    ->badge(),


                Tables\Columns\TextColumn::make('quota_mb')
                    ->label('Quota')
                    ->formatStateUsing(
                        fn ($state) => number_format($state / 1024, 1) . ' GB'
                    ),


                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {

                        'active' => 'success',

                        'pending' => 'warning',

                        'suspended',
                        'disabled' => 'danger',

                        default => 'gray',
                    }),


                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),

            ])
            ->filters([

                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'active' => 'Active',
                        'suspended' => 'Suspended',
                        'disabled' => 'Disabled',
                    ]),

            ])
            ->actions([

                EditAction::make(),

            ])
            ->bulkActions([

                BulkActionGroup::make([]),

            ]);
    }


    public static function getRelations(): array
    {
        return [];
    }


    public static function getPages(): array
    {
        return [

            'index' => Pages\ListMailboxes::route('/'),

            'create' => Pages\CreateMailbox::route('/create'),

            'edit' => Pages\EditMailbox::route('/{record}/edit'),

        ];
    }
}
