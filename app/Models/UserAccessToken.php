<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'created_by',
    'purpose',
    'token_hash',
    'expires_at',
    'used_at',
    'revoked_at',
])]
class UserAccessToken extends Model
{
    use HasFactory;


    public const PURPOSE_ACTIVATION = 'activation';

    public const PURPOSE_PASSWORD_RESET = 'password_reset';


    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


    public function creator(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Status
    |--------------------------------------------------------------------------
    */

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }


    public function isUsed(): bool
    {
        return $this->used_at !== null;
    }


    public function isRevoked(): bool
    {
        return $this->revoked_at !== null;
    }


    public function isValid(): bool
    {
        return ! $this->isExpired()
            && ! $this->isUsed()
            && ! $this->isRevoked();
    }


    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'used_at' => 'datetime',
            'revoked_at' => 'datetime',
        ];
    }
}
