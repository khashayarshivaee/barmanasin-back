<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CapabilityFocusPoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'capability_id',
        'title_en',
        'title_fa',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'capability_id' => 'integer',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function capability(): BelongsTo
    {
        return $this->belongsTo(Capability::class);
    }
}
