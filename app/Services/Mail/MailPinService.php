<?php

namespace App\Services\Mail;

use App\Models\MailPinnedMessage;
use App\Models\User;
use Illuminate\Validation\ValidationException;


class MailPinService
{


    private const MAX_PINS = 5;




    public function list(
        User $user
    ) {

        return MailPinnedMessage::query()

            ->where(
                'user_id',
                $user->id
            )

            ->orderBy(
                'position'
            )

            ->get();

    }





    public function pin(
        User $user,
        string $mailbox,
        string $uid,
    ): MailPinnedMessage {


        $exists =
            MailPinnedMessage::query()

                ->where(
                    'user_id',
                    $user->id
                )

                ->where(
                    'mailbox',
                    $mailbox
                )

                ->where(
                    'message_uid',
                    $uid
                )

                ->exists();



        if ($exists) {

            return MailPinnedMessage::query()

                ->where(
                    'user_id',
                    $user->id
                )

                ->where(
                    'mailbox',
                    $mailbox
                )

                ->where(
                    'message_uid',
                    $uid
                )

                ->first();

        }




        $count =
            MailPinnedMessage::query()

                ->where(
                    'user_id',
                    $user->id
                )

                ->count();



        if ($count >= self::MAX_PINS) {

            throw ValidationException::withMessages([

                'pin' =>
                    'You can pin up to 5 messages.',

            ]);

        }





        return MailPinnedMessage::create([

            'user_id' =>
                $user->id,

            'mailbox' =>
                $mailbox,

            'message_uid' =>
                $uid,

            'position' =>
                $count + 1,

        ]);

    }






    public function unpin(
        User $user,
        string $mailbox,
        string $uid,
    ): void {


        MailPinnedMessage::query()

            ->where(
                'user_id',
                $user->id
            )

            ->where(
                'mailbox',
                $mailbox
            )

            ->where(
                'message_uid',
                $uid
            )

            ->delete();


    }


}
