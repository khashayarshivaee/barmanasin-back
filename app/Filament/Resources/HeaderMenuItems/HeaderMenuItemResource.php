<?php

namespace App\Filament\Resources\HeaderMenuItems;

use App\Filament\Resources\HeaderMenuItems\Pages\CreateHeaderMenuItem;
use App\Filament\Resources\HeaderMenuItems\Pages\EditHeaderMenuItem;
use App\Filament\Resources\HeaderMenuItems\Pages\ListHeaderMenuItems;
use App\Filament\Resources\HeaderMenuItems\Schemas\HeaderMenuItemForm;
use App\Filament\Resources\HeaderMenuItems\Tables\HeaderMenuItemsTable;
use App\Models\HeaderMenuItem;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use App\Filament\Resources\HeaderMenuItems\RelationManagers\MegaMenuSectionsRelationManager;
class HeaderMenuItemResource extends Resource
{
    protected static ?string $model = HeaderMenuItem::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'title_en';

    protected static ?string $modelLabel = 'Menu Item';

    protected static ?string $pluralModelLabel = 'Menu Items';

    protected static ?string $navigationLabel = 'Header Menu';

    protected static string | \UnitEnum | null $navigationGroup = 'Website';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return HeaderMenuItemForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HeaderMenuItemsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            MegaMenuSectionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListHeaderMenuItems::route('/'),
            'create' => CreateHeaderMenuItem::route('/create'),
            'edit' => EditHeaderMenuItem::route('/{record}/edit'),
        ];
    }
}
