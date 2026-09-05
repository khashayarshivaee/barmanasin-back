<?php

namespace App\Filament\Resources\Capabilities\Pages;

use App\Filament\Resources\Capabilities\CapabilityResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCapability extends CreateRecord
{
    protected static string $resource =
        CapabilityResource::class;


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
