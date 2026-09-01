<?php

namespace App\Filament\Resources\HomeFeaturedProjects\Pages;

use App\Filament\Resources\HomeFeaturedProjects\HomeFeaturedProjectResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageHomeFeaturedProjects extends ManageRecords
{
    protected static string $resource = HomeFeaturedProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Add Featured Project'),
        ];
    }
}
