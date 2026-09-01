<?php

namespace App\Filament\Resources\HomeCapabilitiesSections\Pages;

use App\Filament\Resources\HomeCapabilitiesSections\HomeCapabilitiesSectionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageHomeCapabilitiesSections extends ManageRecords
{
    protected static string $resource = HomeCapabilitiesSectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Configure Capabilities')
                ->visible(
                    fn (): bool =>
                    HomeCapabilitiesSectionResource::canCreate()
                ),
        ];
    }
}
