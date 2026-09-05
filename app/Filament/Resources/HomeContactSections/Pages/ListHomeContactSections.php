<?php

namespace App\Filament\Resources\HomeContactSections\Pages;

use App\Filament\Resources\HomeContactSections\HomeContactSectionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListHomeContactSections extends ListRecords
{
    protected static string $resource = HomeContactSectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
