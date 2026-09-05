<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SiteFooter extends Model
{
    protected $fillable = [
        'logo_path',
        'description_en',
        'description_fa',
        'address_en',
        'address_fa',
        'phone',
        'fax',
        'email',
        'copyright_en',
        'copyright_fa',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function links(): HasMany
    {
        return $this->hasMany(
            SiteFooterLink::class,
            'site_footer_id'
        )->orderBy('sort_order');
    }
}
