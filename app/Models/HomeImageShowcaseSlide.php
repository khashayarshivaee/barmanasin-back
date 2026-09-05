<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HomeImageShowcaseSlide extends Model
{
    protected $fillable = [
        'home_image_showcase_section_id',
        'image_path',
        'title_en',
        'title_fa',
        'description_en',
        'description_fa',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function section(): BelongsTo
    {
        return $this->belongsTo(
            HomeImageShowcaseSection::class,
            'home_image_showcase_section_id'
        );
    }
}
