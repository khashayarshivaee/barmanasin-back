<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mailbox extends Model
{
    protected $fillable = [

        'user_id',

        'address',

        'local_part',

        'domain',

        'provider',

        'external_id',

        'quota_mb',

        'used_storage_mb',

        'status',

    ];


    protected function casts(): array
    {
        return [

            'quota_mb' => 'integer',

            'used_storage_mb' => 'integer',

        ];
    }


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


    public function isActive(): bool
    {
        return $this->status === 'active';
    }


    public function getUsagePercentageAttribute(): float
    {
        if ($this->quota_mb <= 0) {
            return 0;
        }

        return round(
            ($this->used_storage_mb / $this->quota_mb) * 100,
            2
        );
    }
}
