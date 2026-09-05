<?php

namespace App\Filament\Resources\HomeImageShowcaseSections\Pages;

use App\Filament\Resources\HomeImageShowcaseSections\HomeImageShowcaseSectionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListHomeImageShowcaseSections extends ListRecords
{
    protected static string $resource = HomeImageShowcaseSectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
