<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'header_menu_item_id',
    'title_en',
    'title_fa',
    'sort_order',
    'is_active',
])]
class HeaderMegaMenuSection extends Model
{
    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function menuItem(): BelongsTo
    {
        return $this->belongsTo(
            HeaderMenuItem::class,
            'header_menu_item_id',
        );
    }

    public function links(): HasMany
    {
        return $this->hasMany(
            HeaderMegaMenuLink::class,
            'header_mega_menu_section_id',
        );
    }
}
