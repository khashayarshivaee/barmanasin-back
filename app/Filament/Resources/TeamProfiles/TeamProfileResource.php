<?php

namespace App\Filament\Resources\TeamProfiles;

use App\Filament\Resources\TeamProfiles\Pages\EditTeamProfile;
use App\Filament\Resources\TeamProfiles\Pages\ListTeamProfiles;
use App\Filament\Resources\TeamProfiles\Schemas\TeamProfileForm;
use App\Filament\Resources\TeamProfiles\Tables\TeamProfilesTable;
use App\Models\TeamProfile;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class TeamProfileResource extends Resource
{
    protected static ?string $model =
        TeamProfile::class;

    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedUserGroup;

    protected static string|UnitEnum|null $navigationGroup =
        'Mail Management';

    protected static ?string $navigationLabel =
        'Team Profiles';

    protected static ?string $modelLabel =
        'Team Profile';

    protected static ?string $pluralModelLabel =
        'Team Profiles';

    protected static ?string $recordTitleAttribute =
        'name_en';

    protected static ?int $navigationSort =
        20;


    public static function form(Schema $schema): Schema
    {
        return TeamProfileForm::configure($schema);
    }


    public static function table(Table $table): Table
    {
        return TeamProfilesTable::configure($table);
    }


    public static function getPages(): array
    {
        return [
            'index' =>
                ListTeamProfiles::route('/'),

            'edit' =>
                EditTeamProfile::route('/{record}/edit'),
        ];
    }
}
