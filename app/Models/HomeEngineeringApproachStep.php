<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HomeEngineeringApproachStep extends Model
{
    protected $fillable = [
        'section_id',

        'title_en',
        'title_fa',

        'description_en',
        'description_fa',

        'sort_order',

        'is_active',
    ];


    protected $casts = [
        'is_active' => 'boolean',
    ];


    public function section(): BelongsTo
    {
        return $this->belongsTo(
            HomeEngineeringApproachSection::class,
            'section_id'
        );
    }
}
