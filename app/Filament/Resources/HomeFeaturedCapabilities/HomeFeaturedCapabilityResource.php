<?php

namespace App\Filament\Resources\HomeFeaturedCapabilities;

use App\Filament\Resources\HomeFeaturedCapabilities\Pages\ManageHomeFeaturedCapabilities;
use App\Filament\Resources\HomeFeaturedCapabilities\Schemas\HomeFeaturedCapabilityForm;
use App\Filament\Resources\HomeFeaturedCapabilities\Tables\HomeFeaturedCapabilitiesTable;
use App\Models\HomeFeaturedCapability;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class HomeFeaturedCapabilityResource extends Resource
{
    protected static ?string $model =
        HomeFeaturedCapability::class;

    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedStar;

    protected static string|UnitEnum|null $navigationGroup =
        'Capabilities';

    protected static ?string $navigationLabel =
        'Featured Capabilities';

    protected static ?string $modelLabel =
        'Featured Capability';

    protected static ?string $pluralModelLabel =
        'Featured Capabilities';

    protected static ?int $navigationSort =
        20;


    public static function form(Schema $schema): Schema
    {
        return HomeFeaturedCapabilityForm::configure($schema);
    }


    public static function table(Table $table): Table
    {
        return HomeFeaturedCapabilitiesTable::configure($table);
    }


    public static function getPages(): array
    {
        return [
            'index' =>
                ManageHomeFeaturedCapabilities::route('/'),
        ];
    }
}
