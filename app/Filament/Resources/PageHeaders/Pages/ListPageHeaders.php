<?php

namespace App\Filament\Resources\PageHeaders\Pages;

use App\Filament\Resources\PageHeaders\PageHeaderResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPageHeaders extends ListRecords
{
    protected static string $resource = PageHeaderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
