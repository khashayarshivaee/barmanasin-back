<?php

namespace App\Filament\Resources\HomeEngineeringApproachSections\Pages;

use App\Filament\Resources\HomeEngineeringApproachSections\HomeEngineeringApproachSectionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListHomeEngineeringApproachSections extends ListRecords
{
    protected static string $resource = HomeEngineeringApproachSectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
