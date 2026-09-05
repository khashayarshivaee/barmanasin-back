<?php

namespace App\Filament\Resources\ProjectCategories;

use App\Filament\Resources\ProjectCategories\Pages\ManageProjectCategories;
use App\Filament\Resources\ProjectCategories\Schemas\ProjectCategoryForm;
use App\Filament\Resources\ProjectCategories\Tables\ProjectCategoriesTable;
use App\Models\ProjectCategory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ProjectCategoryResource extends Resource
{
    protected static ?string $model =
        ProjectCategory::class;

    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedTag;

    protected static string|UnitEnum|null $navigationGroup =
        'Projects';

    protected static ?string $navigationLabel =
        'Categories';

    protected static ?string $modelLabel =
        'Project Category';

    protected static ?string $pluralModelLabel =
        'Project Categories';

    protected static ?string $recordTitleAttribute =
        'name_en';

    protected static ?int $navigationSort =
        20;


    public static function form(Schema $schema): Schema
    {
        return ProjectCategoryForm::configure($schema);
    }


    public static function table(Table $table): Table
    {
        return ProjectCategoriesTable::configure($table);
    }


    public static function getPages(): array
    {
        return [
            'index' =>
                ManageProjectCategories::route('/'),
        ];
    }
}
