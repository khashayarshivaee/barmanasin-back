<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\HomeCapabilitiesSections\HomeCapabilitiesSectionResource;
use App\Filament\Resources\HomeContactSections\HomeContactSectionResource;
use App\Filament\Resources\HomeEngineeringApproachSections\HomeEngineeringApproachSectionResource;
use App\Filament\Resources\HomeFeaturedProjectsSections\HomeFeaturedProjectsSectionResource;
use App\Filament\Resources\HomeImageShowcaseSections\HomeImageShowcaseSectionResource;
use App\Filament\Resources\SiteFooters\SiteFooterResource;
use Filament\Widgets\Widget;

class QuickActions extends Widget
{
    protected string $view =
        'filament.widgets.quick-actions';

    protected int | string | array $columnSpan = 'full';

    protected function getViewData(): array
    {
        return [
            'actions' => [
                [
                    'label' => 'Selected Works',
                    'description' => 'Manage featured projects on the homepage.',
                    'icon' => 'heroicon-o-squares-plus',
                    'url' => HomeFeaturedProjectsSectionResource::getUrl('index'),
                ],
                [
                    'label' => 'Capabilities',
                    'description' => 'Edit the homepage capabilities section.',
                    'icon' => 'heroicon-o-wrench-screwdriver',
                    'url' => HomeCapabilitiesSectionResource::getUrl('index'),
                ],
                [
                    'label' => 'Engineering Approach',
                    'description' => 'Manage process steps and methodology.',
                    'icon' => 'heroicon-o-arrow-path',
                    'url' => HomeEngineeringApproachSectionResource::getUrl('index'),
                ],
                [
                    'label' => 'Contact',
                    'description' => 'Edit the homepage contact call to action.',
                    'icon' => 'heroicon-o-envelope',
                    'url' => HomeContactSectionResource::getUrl('index'),
                ],
                [
                    'label' => 'Image Showcase',
                    'description' => 'Manage visual stories before the footer.',
                    'icon' => 'heroicon-o-photo',
                    'url' => HomeImageShowcaseSectionResource::getUrl('index'),
                ],
                [
                    'label' => 'Footer',
                    'description' => 'Manage footer content, links and contact details.',
                    'icon' => 'heroicon-o-bars-3-bottom-left',
                    'url' => SiteFooterResource::getUrl('index'),
                ],
            ],
        ];
    }
}
