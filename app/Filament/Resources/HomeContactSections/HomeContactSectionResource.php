<?php

namespace App\Filament\Resources\HomeContactSections;

use App\Filament\Resources\HomeContactSections\Pages\ManageHomeContactSections;
use App\Filament\Resources\HomeContactSections\Schemas\HomeContactSectionForm;
use App\Filament\Resources\HomeContactSections\Tables\HomeContactSectionsTable;
use App\Models\HomeContactSection;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class HomeContactSectionResource extends Resource
{
    protected static ?string $model = HomeContactSection::class;

    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedChatBubbleLeftRight;

    protected static string|UnitEnum|null $navigationGroup =
        'Home';

    protected static ?string $navigationLabel =
        'Contact';

    protected static ?string $modelLabel =
        'Contact';

    protected static ?string $pluralModelLabel =
        'Contact';

    protected static ?int $navigationSort =
        50;

    protected static ?string $recordTitleAttribute =
        'title_en';


    public static function form(Schema $schema): Schema
    {
        return HomeContactSectionForm::configure($schema);
    }


    public static function table(Table $table): Table
    {
        return HomeContactSectionsTable::configure($table);
    }


    public static function getRelations(): array
    {
        return [];
    }


    public static function getPages(): array
    {
        return [
            'index' => ManageHomeContactSections::route('/'),
        ];
    }


    public static function canCreate(): bool
    {
        return ! HomeContactSection::query()->exists();
    }
}
