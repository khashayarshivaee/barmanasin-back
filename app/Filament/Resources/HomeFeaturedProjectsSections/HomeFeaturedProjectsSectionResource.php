<?php

namespace App\Filament\Resources\HomeFeaturedProjectsSections;

use App\Filament\Resources\HomeFeaturedProjectsSections\Pages\ManageHomeFeaturedProjectsSections;
use App\Filament\Resources\HomeFeaturedProjectsSections\Schemas\HomeFeaturedProjectsSectionForm;
use App\Filament\Resources\HomeFeaturedProjectsSections\Tables\HomeFeaturedProjectsSectionsTable;
use App\Models\HomeFeaturedProjectsSection;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class HomeFeaturedProjectsSectionResource extends Resource
{
    protected static ?string $model = HomeFeaturedProjectsSection::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-squares-plus';

    protected static string | UnitEnum | null $navigationGroup = 'Home';

    protected static ?string $navigationLabel = 'Selected Works';

    protected static ?string $modelLabel = 'Selected Works';

    protected static ?string $pluralModelLabel = 'Selected Works';

    protected static ?string $recordTitleAttribute = 'title_en';

    protected static ?int $navigationSort = 30;

    public static function form(Schema $schema): Schema
    {
        return HomeFeaturedProjectsSectionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HomeFeaturedProjectsSectionsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageHomeFeaturedProjectsSections::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return ! HomeFeaturedProjectsSection::query()->exists();
    }
}
