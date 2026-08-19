<?php

namespace App\Filament\Resources\HeaderMenuItems\Pages;

use App\Filament\Resources\HeaderMenuItems\HeaderMenuItemResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditHeaderMenuItem extends EditRecord
{
    protected static string $resource = HeaderMenuItemResource::class;

    public function getHeading(): string
    {
        return 'Edit Menu Item';
    }

    public function getSubheading(): ?string
    {
        $englishTitle = trim(
            (string) $this->record->title_en
        );

        $persianTitle = trim(
            (string) $this->record->title_fa
        );

        if ($persianTitle === '') {
            return $englishTitle;
        }

        return "{$englishTitle} · {$persianTitle}";
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->icon('heroicon-m-trash')
                ->iconButton()
                ->tooltip('Delete menu item')
                ->color('danger'),
        ];
    }
}
