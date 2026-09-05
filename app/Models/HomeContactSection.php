<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeContactSection extends Model
{
    protected $fillable = [
        'eyebrow_en',
        'eyebrow_fa',
        'title_en',
        'title_fa',
        'description_en',
        'description_fa',
        'cta_label_en',
        'cta_label_fa',
        'cta_path',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
