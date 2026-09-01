<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Capability extends Model
{
    use HasFactory;

    public const STATUS_DRAFT = 'draft';

    public const STATUS_PUBLISHED = 'published';

    public const STATUS_ARCHIVED = 'archived';

    protected $fillable = [
        'slug',
        'title_en',
        'title_fa',
        'short_description_en',
        'short_description_fa',
        'sort_order',
        'status',
        'published_at',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'published_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function focusPoints(): HasMany
    {
        return $this->hasMany(CapabilityFocusPoint::class)
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function homeFeaturedCapability(): HasOne
    {
        return $this->hasOne(HomeFeaturedCapability::class);
    }
}
