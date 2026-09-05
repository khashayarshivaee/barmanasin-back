<?php

namespace App\Filament\Resources\HeaderMenuItems\Pages;

use App\Filament\Resources\HeaderMenuItems\HeaderMenuItemResource;
use Filament\Resources\Pages\CreateRecord;

class CreateHeaderMenuItem extends CreateRecord
{
    protected static string $resource =
        HeaderMenuItemResource::class;


    protected function getRedirectUrl(): string
    {
        return static::$resource::getUrl(
            'edit',
            [
                'record' => $this->record,
            ]
        );
    }
}
