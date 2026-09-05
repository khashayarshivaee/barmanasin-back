<?php

namespace App\Filament\Resources\HomeEngineeringApproachSections;

use App\Filament\Resources\HomeEngineeringApproachSections\Pages\EditHomeEngineeringApproachSection;
use App\Filament\Resources\HomeEngineeringApproachSections\Pages\ManageHomeEngineeringApproachSections;
use App\Filament\Resources\HomeEngineeringApproachSections\RelationManagers\StepsRelationManager;
use App\Filament\Resources\HomeEngineeringApproachSections\Schemas\HomeEngineeringApproachSectionForm;
use App\Filament\Resources\HomeEngineeringApproachSections\Tables\HomeEngineeringApproachSectionsTable;
use App\Models\HomeEngineeringApproachSection;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class HomeEngineeringApproachSectionResource extends Resource
{
    protected static ?string $model =
        HomeEngineeringApproachSection::class;

    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedArrowPath;

    protected static string|UnitEnum|null $navigationGroup =
        'Home';

    protected static ?string $navigationLabel =
        'Engineering Approach';

    protected static ?string $modelLabel =
        'Engineering Approach';

    protected static ?string $pluralModelLabel =
        'Engineering Approach';

    protected static ?string $recordTitleAttribute =
        'title_en';

    protected static ?int $navigationSort =
        40;


    public static function form(Schema $schema): Schema
    {
        return HomeEngineeringApproachSectionForm::configure($schema);
    }


    public static function table(Table $table): Table
    {
        return HomeEngineeringApproachSectionsTable::configure($table);
    }


    public static function getRelations(): array
    {
        return [
            StepsRelationManager::class,
        ];
    }


    public static function getPages(): array
    {
        return [
            'index' =>
                ManageHomeEngineeringApproachSections::route('/'),

            'edit' =>
                EditHomeEngineeringApproachSection::route('/{record}/edit'),
        ];
    }


    public static function canCreate(): bool
    {
        return ! HomeEngineeringApproachSection::query()->exists();
    }
}
