<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SiteFooterLink extends Model
{
    protected $fillable = [
        'site_footer_id',
        'group',
        'title_en',
        'title_fa',
        'url',
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

    public function footer(): BelongsTo
    {
        return $this->belongsTo(
            SiteFooter::class,
            'site_footer_id'
        );
    }
}
