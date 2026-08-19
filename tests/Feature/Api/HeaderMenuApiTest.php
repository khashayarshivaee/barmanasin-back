<?php

namespace Tests\Feature\Api;

use App\Models\HeaderMegaMenuLink;
use App\Models\HeaderMegaMenuSection;
use App\Models\HeaderMenuItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class HeaderMenuApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_active_header_menu_with_mega_menu_structure(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Main Menu Items
        |--------------------------------------------------------------------------
        */

        $home = HeaderMenuItem::query()->create([
            'title_en' => 'Home',
            'title_fa' => 'صفحه اصلی',
            'path' => '/home',
            'type' => 'link',
            'sort_order' => 20,
            'is_active' => true,
        ]);

        $products = HeaderMenuItem::query()->create([
            'title_en' => 'Products',
            'title_fa' => 'محصولات',
            'path' => '/products',
            'type' => 'mega',
            'sort_order' => 10,
            'is_active' => true,
        ]);

        HeaderMenuItem::query()->create([
            'title_en' => 'Hidden',
            'title_fa' => 'مخفی',
            'path' => '/hidden',
            'type' => 'link',
            'sort_order' => 1,
            'is_active' => false,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Mega Menu Sections
        |--------------------------------------------------------------------------
        */

        $services = HeaderMegaMenuSection::query()->create([
            'header_menu_item_id' => $products->id,
            'title_en' => 'Services',
            'title_fa' => 'خدمات',
            'sort_order' => 20,
            'is_active' => true,
        ]);

        $productsSection = HeaderMegaMenuSection::query()->create([
            'header_menu_item_id' => $products->id,
            'title_en' => 'Products',
            'title_fa' => 'محصولات',
            'sort_order' => 10,
            'is_active' => true,
        ]);

        HeaderMegaMenuSection::query()->create([
            'header_menu_item_id' => $products->id,
            'title_en' => 'Hidden Section',
            'title_fa' => 'بخش مخفی',
            'sort_order' => 1,
            'is_active' => false,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Mega Menu Links
        |--------------------------------------------------------------------------
        */

        $iconPath = 'header/mega-menu/icons/qr-menu.png';

        HeaderMegaMenuLink::query()->create([
            'header_mega_menu_section_id' => $productsSection->id,
            'title_en' => 'QR Menu',
            'title_fa' => 'منوی QR',
            'description_en' => 'Digital menu platform',
            'description_fa' => 'پلتفرم منوی دیجیتال',
            'path' => '/project',
            'link_type' => 'internal',
            'open_in_new_tab' => false,
            'icon' => $iconPath,
            'sort_order' => 10,
            'is_active' => true,
        ]);

        HeaderMegaMenuLink::query()->create([
            'header_mega_menu_section_id' => $productsSection->id,
            'title_en' => 'Task Manager',
            'title_fa' => 'مدیریت وظایف',
            'description_en' => 'Task management platform',
            'description_fa' => 'پلتفرم مدیریت وظایف',
            'path' => '/task-manager',
            'link_type' => 'internal',
            'open_in_new_tab' => false,
            'icon' => null,
            'sort_order' => 20,
            'is_active' => true,
        ]);

        HeaderMegaMenuLink::query()->create([
            'header_mega_menu_section_id' => $productsSection->id,
            'title_en' => 'Hidden Link',
            'title_fa' => 'لینک مخفی',
            'description_en' => null,
            'description_fa' => null,
            'path' => '/hidden-link',
            'link_type' => 'internal',
            'open_in_new_tab' => false,
            'icon' => null,
            'sort_order' => 1,
            'is_active' => false,
        ]);

        HeaderMegaMenuLink::query()->create([
            'header_mega_menu_section_id' => $services->id,
            'title_en' => 'External Service',
            'title_fa' => 'سرویس خارجی',
            'description_en' => 'External website',
            'description_fa' => 'وب‌سایت خارجی',
            'path' => 'https://example.com',
            'link_type' => 'external',
            'open_in_new_tab' => true,
            'icon' => null,
            'sort_order' => 10,
            'is_active' => true,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Request
        |--------------------------------------------------------------------------
        */

        $response = $this->getJson('/api/header/menu');

        /*
        |--------------------------------------------------------------------------
        | Main Response
        |--------------------------------------------------------------------------
        */

        $response
            ->assertOk()
            ->assertJsonCount(2, 'data')

            // Products must come before Home.
            ->assertJsonPath(
                'data.0.id',
                $products->id,
            )
            ->assertJsonPath(
                'data.0.title.en',
                'Products',
            )
            ->assertJsonPath(
                'data.0.title.fa',
                'محصولات',
            )
            ->assertJsonPath(
                'data.0.path',
                '/products',
            )
            ->assertJsonPath(
                'data.0.type',
                'mega',
            )
            ->assertJsonPath(
                'data.0.order',
                10,
            )

            ->assertJsonPath(
                'data.1.id',
                $home->id,
            )
            ->assertJsonPath(
                'data.1.title.en',
                'Home',
            )
            ->assertJsonPath(
                'data.1.type',
                'link',
            )
            ->assertJsonPath(
                'data.1.sections',
                [],
            );

        /*
        |--------------------------------------------------------------------------
        | Section Ordering
        |--------------------------------------------------------------------------
        */

        $response
            ->assertJsonCount(
                2,
                'data.0.sections',
            )
            ->assertJsonPath(
                'data.0.sections.0.title.en',
                'Products',
            )
            ->assertJsonPath(
                'data.0.sections.0.title.fa',
                'محصولات',
            )
            ->assertJsonPath(
                'data.0.sections.0.order',
                10,
            )
            ->assertJsonPath(
                'data.0.sections.1.title.en',
                'Services',
            )
            ->assertJsonPath(
                'data.0.sections.1.order',
                20,
            );

        /*
        |--------------------------------------------------------------------------
        | Link Ordering & Data
        |--------------------------------------------------------------------------
        */

        $response
            ->assertJsonCount(
                2,
                'data.0.sections.0.links',
            )
            ->assertJsonPath(
                'data.0.sections.0.links.0.title.en',
                'QR Menu',
            )
            ->assertJsonPath(
                'data.0.sections.0.links.0.title.fa',
                'منوی QR',
            )
            ->assertJsonPath(
                'data.0.sections.0.links.0.description.en',
                'Digital menu platform',
            )
            ->assertJsonPath(
                'data.0.sections.0.links.0.description.fa',
                'پلتفرم منوی دیجیتال',
            )
            ->assertJsonPath(
                'data.0.sections.0.links.0.path',
                '/project',
            )
            ->assertJsonPath(
                'data.0.sections.0.links.0.type',
                'internal',
            )
            ->assertJsonPath(
                'data.0.sections.0.links.0.open_in_new_tab',
                false,
            )
            ->assertJsonPath(
                'data.0.sections.0.links.0.order',
                10,
            )
            ->assertJsonPath(
                'data.0.sections.0.links.1.title.en',
                'Task Manager',
            );

        /*
        |--------------------------------------------------------------------------
        | External Link
        |--------------------------------------------------------------------------
        */

        $response
            ->assertJsonPath(
                'data.0.sections.1.links.0.title.en',
                'External Service',
            )
            ->assertJsonPath(
                'data.0.sections.1.links.0.type',
                'external',
            )
            ->assertJsonPath(
                'data.0.sections.1.links.0.path',
                'https://example.com',
            )
            ->assertJsonPath(
                'data.0.sections.1.links.0.open_in_new_tab',
                true,
            );

        /*
        |--------------------------------------------------------------------------
        | Icon URL
        |--------------------------------------------------------------------------
        */

        $this->assertSame(
            Storage::disk('public')->url($iconPath),
            $response->json(
                'data.0.sections.0.links.0.icon',
            ),
        );

        /*
        |--------------------------------------------------------------------------
        | Inactive Records Must Never Be Exposed
        |--------------------------------------------------------------------------
        */

        $json = $response->json();

        $this->assertNotContains(
            'Hidden',
            collect($json['data'])
                ->pluck('title.en')
                ->all(),
        );

        $this->assertNotContains(
            'Hidden Section',
            collect($json['data'][0]['sections'])
                ->pluck('title.en')
                ->all(),
        );

        $this->assertNotContains(
            'Hidden Link',
            collect($json['data'][0]['sections'][0]['links'])
                ->pluck('title.en')
                ->all(),
        );
    }
}
