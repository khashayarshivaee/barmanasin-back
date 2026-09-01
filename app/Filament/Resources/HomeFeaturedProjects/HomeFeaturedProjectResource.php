<?php

namespace App\Filament\Resources\HomeFeaturedProjects;

use App\Filament\Resources\HomeFeaturedProjects\Pages\ManageHomeFeaturedProjects;
use App\Filament\Resources\HomeFeaturedProjects\Schemas\HomeFeaturedProjectForm;
use App\Filament\Resources\HomeFeaturedProjects\Tables\HomeFeaturedProjectsTable;
use App\Models\HomeFeaturedProject;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class HomeFeaturedProjectResource extends Resource
{
    protected static ?string $model = HomeFeaturedProject::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-star';

    protected static string | UnitEnum | null $navigationGroup = 'Projects';

    protected static ?string $navigationLabel = 'Featured Projects';

    protected static ?string $modelLabel = 'Featured Project';

    protected static ?string $pluralModelLabel = 'Featured Projects';

    protected static ?int $navigationSort = 30;

    public static function form(Schema $schema): Schema
    {
        return HomeFeaturedProjectForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HomeFeaturedProjectsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageHomeFeaturedProjects::route('/'),
        ];
    }
}
