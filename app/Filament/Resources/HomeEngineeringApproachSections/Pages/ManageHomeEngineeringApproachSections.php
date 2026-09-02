<?php

namespace App\Filament\Resources\HomeEngineeringApproachSections\Pages;

use App\Filament\Resources\HomeEngineeringApproachSections\HomeEngineeringApproachSectionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageHomeEngineeringApproachSections extends ManageRecords
{
    protected static string $resource =
        HomeEngineeringApproachSectionResource::class;


    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Configure Engineering Approach')
                ->visible(
                    fn (): bool =>
                    HomeEngineeringApproachSectionResource::canCreate()
                )
                ->successRedirectUrl(
                    fn ($record): string =>
                    HomeEngineeringApproachSectionResource::getUrl(
                        'edit',
                        [
                            'record' => $record,
                        ]
                    )
                ),
        ];
    }
}
