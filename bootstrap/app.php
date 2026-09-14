<?php

use Illuminate\Support\Facades\Broadcast;


Broadcast::channel(
    'App.Models.User.{id}',
    function ($user, $id) {

        return (int) $user->id === (int) $id;

    }
);


Broadcast::channel(
    'mailbox.{userId}',
    function ($user, $userId) {

        \Log::info('Broadcast debug', [
            'user_id' => $user?->id,
            'target_user_id' => $userId,
        ]);


        return true;

    }
);
