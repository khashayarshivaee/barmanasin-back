<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'header_mega_menu_section_id',
    'title_en',
    'title_fa',
    'description_en',
    'description_fa',
    'path',
    'link_type',
    'open_in_new_tab',
    'icon',
    'sort_order',
    'is_active',
])]
class HeaderMegaMenuLink extends Model
{
    protected function casts(): array
    {
        return [
            'open_in_new_tab' => 'boolean',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(
            HeaderMegaMenuSection::class,
            'header_mega_menu_section_id',
        );
    }
}
