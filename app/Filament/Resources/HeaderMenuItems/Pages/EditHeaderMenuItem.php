<?php

namespace App\Filament\Resources\HeaderMenuItems\Pages;

use App\Filament\Resources\HeaderMenuItems\HeaderMenuItemResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditHeaderMenuItem extends EditRecord
{
    protected static string $resource = HeaderMenuItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
