<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MailAttachment extends Model
{
    protected $fillable = [
        'user_id',
        'original_name',
        'path',
        'mime_type',
        'size',
        'stored_name',
        'disk',
        'status',
        'checksum',
        'is_used',
        'used_at',
        'expires_at',
    ];


    protected $casts = [
        'is_used' => 'boolean',
        'used_at' => 'datetime',
        'expires_at' => 'datetime',
    ];


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
