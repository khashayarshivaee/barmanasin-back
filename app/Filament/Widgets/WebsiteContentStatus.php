<?php

namespace App\Filament\Widgets;

use App\Filament\Pages\ManageHomeHero;
use App\Filament\Pages\ManageHomeIntro;
use App\Filament\Resources\HomeCapabilitiesSections\HomeCapabilitiesSectionResource;
use App\Filament\Resources\HomeContactSections\HomeContactSectionResource;
use App\Filament\Resources\HomeEngineeringApproachSections\HomeEngineeringApproachSectionResource;
use App\Filament\Resources\HomeFeaturedProjectsSections\HomeFeaturedProjectsSectionResource;
use App\Filament\Resources\HomeImageShowcaseSections\HomeImageShowcaseSectionResource;
use App\Models\HomeCapabilitiesSection;
use App\Models\HomeContactSection;
use App\Models\HomeEngineeringApproachSection;
use App\Models\HomeFeaturedProjectsSection;
use App\Models\HomeHero;
use App\Models\HomeImageShowcaseSection;
use App\Models\HomeIntro;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class WebsiteContentStatus extends Widget
{
    protected string $view =
        'filament.widgets.website-content-status';

    protected int | string | array $columnSpan = [
        'md' => 2,
        'xl' => 1,
    ];

    protected function getViewData(): array
    {
        $items = [
            [
                'label' => 'Hero',
                'description' => 'Primary homepage presentation',
                'active' => $this->isSectionActive(HomeHero::class),
                'url' => ManageHomeHero::getUrl(),
            ],
            [
                'label' => 'Intro',
                'description' => 'Company introduction',
                'active' => $this->isSectionActive(HomeIntro::class),
                'url' => ManageHomeIntro::getUrl(),
            ],
            [
                'label' => 'Selected Works',
                'description' => 'Featured project presentation',
                'active' => $this->isSectionActive(
                    HomeFeaturedProjectsSection::class
                ),
                'url' => HomeFeaturedProjectsSectionResource::getUrl('index'),
            ],
            [
                'label' => 'Capabilities',
                'description' => 'Homepage capabilities section',
                'active' => $this->isSectionActive(
                    HomeCapabilitiesSection::class
                ),
                'url' => HomeCapabilitiesSectionResource::getUrl('index'),
            ],
            [
                'label' => 'Engineering Approach',
                'description' => 'Process and methodology',
                'active' => $this->isSectionActive(
                    HomeEngineeringApproachSection::class
                ),
                'url' => HomeEngineeringApproachSectionResource::getUrl('index'),
            ],
            [
                'label' => 'Contact',
                'description' => 'Homepage contact call to action',
                'active' => $this->isSectionActive(
                    HomeContactSection::class
                ),
                'url' => HomeContactSectionResource::getUrl('index'),
            ],
            [
                'label' => 'Image Showcase',
                'description' => 'Pre-footer visual stories',
                'active' => $this->isSectionActive(
                    HomeImageShowcaseSection::class
                ),
                'url' => HomeImageShowcaseSectionResource::getUrl('index'),
            ],
        ];

        return [
            'items' => $items,

            'activeCount' => collect($items)
                ->where('active', true)
                ->count(),
        ];
    }

    /**
     * Treat a section as active when:
     * - a record exists, and
     * - is_active is true when that column exists.
     *
     * This also supports older singleton models that may not
     * have an is_active column.
     */
    private function isSectionActive(string $modelClass): bool
    {
        /** @var Model $model */
        $model = new $modelClass();

        $record = $modelClass::query()->first();

        if (! $record) {
            return false;
        }

        if (! Schema::hasColumn($model->getTable(), 'is_active')) {
            return true;
        }

        return (bool) $record->is_active;
    }
}
