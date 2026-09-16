<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MailPinnedMessage extends Model
{

    protected $fillable = [
        'user_id',
        'mailbox',
        'message_uid',
        'position',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
