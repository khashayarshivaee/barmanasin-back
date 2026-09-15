<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class MailboxWatchState extends Model
{
    protected $fillable = [
        'mailbox_id',
        'message_key',
        'detected_at',
    ];


    protected function casts(): array
    {
        return [
            'detected_at' => 'datetime',
        ];
    }


    public function mailbox(): BelongsTo
    {
        return $this->belongsTo(
            Mailbox::class
        );
    }
}
