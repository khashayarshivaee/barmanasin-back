<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomeCapabilitiesSection extends Model
{
    use HasFactory;

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
}
