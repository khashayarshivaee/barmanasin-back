<?php

namespace Tests\Feature\Api;

use App\Models\HeaderMenuItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HeaderMenuApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_only_active_header_menu_items_in_order(): void
    {
        HeaderMenuItem::query()->create([
            'title_en' => 'Projects',
            'title_fa' => 'پروژه‌ها',
            'path' => '/projects',
            'sort_order' => 2,
            'is_active' => true,
        ]);

        HeaderMenuItem::query()->create([
            'title_en' => 'Home',
            'title_fa' => 'خانه',
            'path' => '/',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        HeaderMenuItem::query()->create([
            'title_en' => 'Hidden',
            'title_fa' => 'مخفی',
            'path' => '/hidden',
            'sort_order' => 3,
            'is_active' => false,
        ]);

        $response = $this->getJson('/api/header/menu');

        $response
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.title.en', 'Home')
            ->assertJsonPath('data.0.title.fa', 'خانه')
            ->assertJsonPath('data.0.path', '/')
            ->assertJsonPath('data.1.title.en', 'Projects')
            ->assertJsonPath('data.1.title.fa', 'پروژه‌ها')
            ->assertJsonMissing([
                'path' => '/hidden',
            ]);
    }
}
