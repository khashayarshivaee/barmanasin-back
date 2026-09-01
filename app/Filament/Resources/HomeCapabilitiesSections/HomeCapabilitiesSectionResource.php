<?php

namespace App\Filament\Resources\HomeCapabilitiesSections;

use App\Filament\Resources\HomeCapabilitiesSections\Pages\ManageHomeCapabilitiesSections;
use App\Filament\Resources\HomeCapabilitiesSections\Schemas\HomeCapabilitiesSectionForm;
use App\Filament\Resources\HomeCapabilitiesSections\Tables\HomeCapabilitiesSectionsTable;
use App\Models\HomeCapabilitiesSection;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class HomeCapabilitiesSectionResource extends Resource
{
    protected static ?string $model = HomeCapabilitiesSection::class;

    protected static string | BackedEnum | null $navigationIcon =
        'heroicon-o-wrench-screwdriver';

    protected static string | UnitEnum | null $navigationGroup =
        'Home';

    protected static ?string $navigationLabel =
        'Capabilities';

    protected static ?string $modelLabel =
        'Home Capabilities';

    protected static ?string $pluralModelLabel =
        'Home Capabilities';

    protected static ?string $recordTitleAttribute =
        'title_en';

    protected static ?int $navigationSort = 40;

    public static function form(Schema $schema): Schema
    {
        return HomeCapabilitiesSectionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HomeCapabilitiesSectionsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageHomeCapabilitiesSections::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return ! HomeCapabilitiesSection::query()->exists();
    }
}
