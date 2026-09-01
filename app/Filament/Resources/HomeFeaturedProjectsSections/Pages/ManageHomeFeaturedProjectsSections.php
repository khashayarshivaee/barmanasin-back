<?php

namespace App\Filament\Resources\HomeFeaturedProjectsSections\Pages;

use App\Filament\Resources\HomeFeaturedProjectsSections\HomeFeaturedProjectsSectionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageHomeFeaturedProjectsSections extends ManageRecords
{
    protected static string $resource = HomeFeaturedProjectsSectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Configure Selected Works')
                ->visible(
                    fn (): bool => HomeFeaturedProjectsSectionResource::canCreate()
                ),
        ];
    }
}
