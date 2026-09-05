<?php

namespace App\Filament\Resources\HomeContactSections\Pages;

use App\Filament\Resources\HomeContactSections\HomeContactSectionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditHomeContactSection extends EditRecord
{
    protected static string $resource = HomeContactSectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
