<?php

namespace App\Filament\Resources\HomeFeaturedCapabilities\Pages;

use App\Filament\Resources\HomeFeaturedCapabilities\HomeFeaturedCapabilityResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageHomeFeaturedCapabilities extends ManageRecords
{
    protected static string $resource =
        HomeFeaturedCapabilityResource::class;


    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Add Featured Capability'),
        ];
    }
}
