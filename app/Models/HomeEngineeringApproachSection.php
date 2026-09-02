<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HomeEngineeringApproachSection extends Model
{
    protected $fillable = [
        'eyebrow_en',
        'eyebrow_fa',

        'title_en',
        'title_fa',

        'description_en',
        'description_fa',

        'is_active',
    ];


    protected $casts = [
        'is_active' => 'boolean',
    ];


    public function steps(): HasMany
    {
        return $this->hasMany(
            HomeEngineeringApproachStep::class,
            'section_id'
        )
            ->orderBy('sort_order');
    }
}
