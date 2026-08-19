<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeHero extends Model
{
    protected $fillable = [
        'eyebrow_en',
        'eyebrow_fa',

        'title_en',
        'title_fa',

        'description_en',
        'description_fa',

        'fullscreen_caption_en',
        'fullscreen_caption_fa',

        'cta_title_en',
        'cta_title_fa',
        'cta_path',

        'desktop_image',
        'mobile_image',

        'image_alt_en',
        'image_alt_fa',

        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
