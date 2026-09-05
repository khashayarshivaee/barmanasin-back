<?php

namespace App\Filament\Resources\SiteFooters\Pages;

use App\Filament\Resources\SiteFooters\SiteFooterResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSiteFooters extends ListRecords
{
    protected static string $resource = SiteFooterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
