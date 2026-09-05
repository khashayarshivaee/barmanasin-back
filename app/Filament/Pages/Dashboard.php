<?php

namespace App\Filament\Pages;

use App\Filament\Resources\ContactInquiries\ContactInquiryResource;
use App\Filament\Resources\Projects\ProjectResource;
use Filament\Actions\Action;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    public static function getNavigationLabel(): string
    {
        return 'Control Center';
    }

    public function getHeading(): string
    {
        return 'BARMANASIN / CONTROL CENTER';
    }

    public function getSubheading(): ?string
    {
        return 'Website content, projects and inquiries at a glance.';
    }

    public function getColumns(): int | array
    {
        return [
            'md' => 2,
            'xl' => 4,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('viewWebsite')
                ->label('View Website')
                ->icon('heroicon-m-arrow-top-right-on-square')
                ->color('gray')
                ->url('https://barmanasin.com')
                ->openUrlInNewTab(),

            Action::make('inquiries')
                ->label('View Inquiries')
                ->icon('heroicon-m-envelope')
                ->color('gray')
                ->url(
                    ContactInquiryResource::getUrl('index')
                ),

            Action::make('newProject')
                ->label('New Project')
                ->icon('heroicon-m-plus')
                ->url(
                    ProjectResource::getUrl('create')
                ),
        ];
    }
}
