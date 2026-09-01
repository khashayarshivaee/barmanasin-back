<?php

namespace App\Filament\Resources\Capabilities;

use App\Filament\Resources\Capabilities\Pages\CreateCapability;
use App\Filament\Resources\Capabilities\Pages\EditCapability;
use App\Filament\Resources\Capabilities\Pages\ListCapabilities;
use App\Filament\Resources\Capabilities\RelationManagers\FocusPointsRelationManager;
use App\Filament\Resources\Capabilities\Schemas\CapabilityForm;
use App\Filament\Resources\Capabilities\Tables\CapabilitiesTable;
use App\Models\Capability;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class CapabilityResource extends Resource
{
    protected static ?string $model = Capability::class;

    protected static string | BackedEnum | null $navigationIcon =
        'heroicon-o-cpu-chip';

    protected static string | UnitEnum | null $navigationGroup =
        'Capabilities';

    protected static ?string $navigationLabel =
        'Capabilities';

    protected static ?string $modelLabel =
        'Capability';

    protected static ?string $pluralModelLabel =
        'Capabilities';

    protected static ?string $recordTitleAttribute =
        'title_en';

    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return CapabilityForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CapabilitiesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            FocusPointsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCapabilities::route('/'),
            'create' => CreateCapability::route('/create'),
            'edit' => EditCapability::route('/{record}/edit'),
        ];
    }
}
