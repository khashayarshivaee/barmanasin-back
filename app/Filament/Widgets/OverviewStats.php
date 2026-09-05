<?php

namespace App\Filament\Widgets;

use App\Models\Capability;
use App\Models\ContactInquiry;
use App\Models\Project;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OverviewStats extends StatsOverviewWidget
{
    protected ?string $heading = 'Overview';

    protected ?string $description =
        'Current website and business activity.';

    protected function getStats(): array
    {
        $projectsCount = Project::query()->count();

        $publishedProjectsCount = Project::query()
            ->where('status', 'published')
            ->count();

        $capabilitiesCount = Capability::query()
            ->count();

        $activeCapabilitiesCount = Capability::query()
            ->where('is_active', true)
            ->count();

        $newInquiriesCount = ContactInquiry::query()
            ->where('status', 'new')
            ->count();

        $totalInquiriesCount = ContactInquiry::query()
            ->count();

        return [
            Stat::make(
                'Projects',
                number_format($projectsCount)
            )
                ->description(
                    number_format($publishedProjectsCount)
                    . ' published'
                )
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('gray'),

            Stat::make(
                'Capabilities',
                number_format($capabilitiesCount)
            )
                ->description(
                    number_format($activeCapabilitiesCount)
                    . ' active'
                )
                ->descriptionIcon('heroicon-m-cpu-chip')
                ->color('gray'),

            Stat::make(
                'New Inquiries',
                number_format($newInquiriesCount)
            )
                ->description(
                    number_format($totalInquiriesCount)
                    . ' total inquiries'
                )
                ->descriptionIcon('heroicon-m-envelope')
                ->color(
                    $newInquiriesCount > 0
                        ? 'warning'
                        : 'gray'
                ),

            Stat::make(
                'Website Status',
                'Live'
            )
                ->description('Production website')
                ->descriptionIcon('heroicon-m-globe-alt')
                ->color('success'),
        ];
    }
}
