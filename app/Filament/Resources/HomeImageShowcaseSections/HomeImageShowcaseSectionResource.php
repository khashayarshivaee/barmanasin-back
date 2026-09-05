<?php

namespace App\Filament\Resources\HomeImageShowcaseSections;

use App\Filament\Resources\HomeImageShowcaseSections\Pages\CreateHomeImageShowcaseSection;
use App\Filament\Resources\HomeImageShowcaseSections\Pages\EditHomeImageShowcaseSection;
use App\Filament\Resources\HomeImageShowcaseSections\Pages\ListHomeImageShowcaseSections;
use App\Filament\Resources\HomeImageShowcaseSections\RelationManagers\SlidesRelationManager;
use App\Filament\Resources\HomeImageShowcaseSections\Schemas\HomeImageShowcaseSectionForm;
use App\Filament\Resources\HomeImageShowcaseSections\Tables\HomeImageShowcaseSectionsTable;
use App\Models\HomeImageShowcaseSection;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class HomeImageShowcaseSectionResource extends Resource
{
    protected static ?string $model =
        HomeImageShowcaseSection::class;

    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedPhoto;

    protected static string|UnitEnum|null $navigationGroup =
        'Home';

    protected static ?string $navigationLabel =
        'Image Showcase';

    protected static ?string $modelLabel =
        'Image Showcase';

    protected static ?string $pluralModelLabel =
        'Image Showcase';

    protected static ?string $recordTitleAttribute =
        'title_en';

    protected static ?int $navigationSort =
        50;


    public static function form(Schema $schema): Schema
    {
        return HomeImageShowcaseSectionForm::configure($schema);
    }


    public static function table(Table $table): Table
    {
        return HomeImageShowcaseSectionsTable::configure($table);
    }


    public static function getRelations(): array
    {
        return [
            SlidesRelationManager::class,
        ];
    }


    public static function getPages(): array
    {
        return [
            'index' =>
                ListHomeImageShowcaseSections::route('/'),

            'create' =>
                CreateHomeImageShowcaseSection::route('/create'),

            'edit' =>
                EditHomeImageShowcaseSection::route('/{record}/edit'),
        ];
    }


    public static function canCreate(): bool
    {
        return ! HomeImageShowcaseSection::query()->exists();
    }
}
