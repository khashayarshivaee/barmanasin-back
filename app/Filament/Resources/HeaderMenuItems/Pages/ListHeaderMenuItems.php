<?php

namespace App\Filament\Resources\HeaderMenuItems\Pages;

use App\Filament\Resources\HeaderMenuItems\HeaderMenuItemResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListHeaderMenuItems extends ListRecords
{
    protected static string $resource =
        HeaderMenuItemResource::class;


    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('New Menu Item'),
        ];
    }
}
