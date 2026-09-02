<?php

namespace App\Filament\Resources\HomeEngineeringApproachSections\Pages;

use App\Filament\Resources\HomeEngineeringApproachSections\HomeEngineeringApproachSectionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditHomeEngineeringApproachSection extends EditRecord
{
    protected static string $resource =
        HomeEngineeringApproachSectionResource::class;


    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
