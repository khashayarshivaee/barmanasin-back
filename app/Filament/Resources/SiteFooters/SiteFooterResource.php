<?php

namespace App\Filament\Resources\SiteFooters;

use App\Filament\Resources\SiteFooters\Pages\CreateSiteFooter;
use App\Filament\Resources\SiteFooters\Pages\EditSiteFooter;
use App\Filament\Resources\SiteFooters\Pages\ListSiteFooters;
use App\Filament\Resources\SiteFooters\RelationManagers\LinksRelationManager;
use App\Filament\Resources\SiteFooters\Schemas\SiteFooterForm;
use App\Filament\Resources\SiteFooters\Tables\SiteFootersTable;
use App\Models\SiteFooter;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class SiteFooterResource extends Resource
{
    protected static ?string $model = SiteFooter::class;

    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedRectangleGroup;

    protected static string|UnitEnum|null $navigationGroup =
        'Website';

    protected static ?string $navigationLabel =
        'Footer';

    protected static ?string $modelLabel =
        'Footer';

    protected static ?string $pluralModelLabel =
        'Footer';

    protected static ?string $recordTitleAttribute =
        'copyright_en';

    protected static ?int $navigationSort =
        40;


    public static function form(Schema $schema): Schema
    {
        return SiteFooterForm::configure($schema);
    }


    public static function table(Table $table): Table
    {
        return SiteFootersTable::configure($table);
    }


    public static function getRelations(): array
    {
        return [
            LinksRelationManager::class,
        ];
    }


    public static function getPages(): array
    {
        return [
            'index' =>
                ListSiteFooters::route('/'),

            'create' =>
                CreateSiteFooter::route('/create'),

            'edit' =>
                EditSiteFooter::route('/{record}/edit'),
        ];
    }


    public static function canCreate(): bool
    {
        return ! SiteFooter::query()->exists();
    }
}
