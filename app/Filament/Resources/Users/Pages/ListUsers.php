<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected static string $resource =
        UserResource::class;


    public function getHeading(): string
    {
        return 'Users';
    }


    public function getSubheading(): ?string
    {
        return 'Manage company accounts, mailbox access and user security.';
    }


    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('New User')
                ->icon('heroicon-m-user-plus'),
        ];
    }
}
