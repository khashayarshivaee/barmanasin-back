<?php

namespace App\Filament\Resources\ContactInquiries;

use App\Filament\Resources\ContactInquiries\Pages\ManageContactInquiries;
use App\Filament\Resources\ContactInquiries\Schemas\ContactInquiryForm;
use App\Filament\Resources\ContactInquiries\Tables\ContactInquiriesTable;
use App\Models\ContactInquiry;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ContactInquiryResource extends Resource
{
    protected static ?string $model = ContactInquiry::class;

    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedEnvelope;

    protected static string|UnitEnum|null $navigationGroup =
        'Contact';

    protected static ?string $navigationLabel =
        'Inquiries';

    protected static ?string $modelLabel =
        'Inquiry';

    protected static ?string $pluralModelLabel =
        'Inquiries';

    protected static ?string $recordTitleAttribute =
        'name';

    protected static ?int $navigationSort =
        10;


    public static function form(Schema $schema): Schema
    {
        return ContactInquiryForm::configure($schema);
    }


    public static function table(Table $table): Table
    {
        return ContactInquiriesTable::configure($table);
    }


    public static function getRelations(): array
    {
        return [];
    }


    public static function getPages(): array
    {
        return [
            'index' => ManageContactInquiries::route('/'),
        ];
    }


    public static function canCreate(): bool
    {
        return false;
    }

    public static function getNavigationBadge(): ?string
    {
        $count = ContactInquiry::query()
            ->where('status', 'new')
            ->count();

        return $count > 0
            ? (string) $count
            : null;
    }


    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'danger';
    }
}
