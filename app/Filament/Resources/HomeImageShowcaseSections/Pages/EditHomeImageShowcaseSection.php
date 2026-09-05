<?php

namespace App\Filament\Resources\HomeImageShowcaseSections\Pages;

use App\Filament\Resources\HomeImageShowcaseSections\HomeImageShowcaseSectionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditHomeImageShowcaseSection extends EditRecord
{
    protected static string $resource = HomeImageShowcaseSectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
