<?php

namespace App\Filament\Resources\PageHeaders\Pages;

use App\Filament\Resources\PageHeaders\PageHeaderResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPageHeader extends EditRecord
{
    protected static string $resource = PageHeaderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
