<?php

namespace App\Filament\Resources\PageHeaders;

use App\Filament\Resources\PageHeaders\Pages\CreatePageHeader;
use App\Filament\Resources\PageHeaders\Pages\EditPageHeader;
use App\Filament\Resources\PageHeaders\Pages\ListPageHeaders;
use App\Filament\Resources\PageHeaders\Schemas\PageHeaderForm;
use App\Filament\Resources\PageHeaders\Tables\PageHeadersTable;
use App\Models\PageHeader;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PageHeaderResource extends Resource
{
    protected static ?string $model = PageHeader::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return PageHeaderForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PageHeadersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPageHeaders::route('/'),
            'create' => CreatePageHeader::route('/create'),
            'edit' => EditPageHeader::route('/{record}/edit'),
        ];
    }
}
