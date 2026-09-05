<?php

namespace App\Filament\Resources\HomeContactSections\Pages;

use App\Filament\Resources\HomeContactSections\HomeContactSectionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageHomeContactSections extends ManageRecords
{
    protected static string $resource =
        HomeContactSectionResource::class;


    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Configure Contact')
                ->visible(
                    fn (): bool =>
                    HomeContactSectionResource::canCreate()
                ),
        ];
    }
}
