<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HomeImageShowcaseSection extends Model
{
    protected $fillable = [
        'eyebrow_en',
        'eyebrow_fa',
        'title_en',
        'title_fa',
        'description_en',
        'description_fa',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function slides(): HasMany
    {
        return $this->hasMany(
            HomeImageShowcaseSlide::class,
            'home_image_showcase_section_id'
        )->orderBy('sort_order');
    }
}
