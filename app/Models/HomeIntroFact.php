<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HomeIntroFact extends Model
{
    protected $fillable = [
        'home_intro_id',
        'value',
        'label_en',
        'label_fa',
        'suffix_en',
        'suffix_fa',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function homeIntro(): BelongsTo
    {
        return $this->belongsTo(HomeIntro::class);
    }
}
