<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Project extends Model
{
    use HasFactory;

    public const STATUS_DRAFT = 'draft';

    public const STATUS_PUBLISHED = 'published';

    public const STATUS_ARCHIVED = 'archived';

    protected $fillable = [
        'project_category_id',
        'slug',
        'title_en',
        'title_fa',
        'short_description_en',
        'short_description_fa',
        'location_en',
        'location_fa',
        'year',
        'cover_image_path',
        'mobile_cover_image_path',
        'status',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'project_category_id' => 'integer',
            'year' => 'integer',
            'published_at' => 'datetime',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProjectCategory::class, 'project_category_id');
    }

    public function homeFeaturedProject(): HasOne
    {
        return $this->hasOne(HomeFeaturedProject::class);
    }
}
