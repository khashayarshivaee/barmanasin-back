<?php

namespace App\Filament\Resources\SiteFooters\Pages;

use App\Filament\Resources\SiteFooters\SiteFooterResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSiteFooter extends EditRecord
{
    protected static string $resource = SiteFooterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
