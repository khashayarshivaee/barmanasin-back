<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HomeIntro extends Model
{
    protected $fillable = [
        'eyebrow_en',
        'eyebrow_fa',
        'title_en',
        'title_fa',
        'description_en',
        'description_fa',
        'cta_title_en',
        'cta_title_fa',
        'cta_path',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function facts(): HasMany
    {
        return $this->hasMany(HomeIntroFact::class)
            ->orderBy('sort_order')
            ->orderBy('id');
    }
}
