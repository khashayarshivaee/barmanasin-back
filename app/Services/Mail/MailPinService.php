<?php

namespace App\Services\Mail;

use App\Models\MailPinnedMessage;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use App\Services\Mail\MailboxReaderService;

class MailPinService
{


    private const MAX_PINS = 5;

    public function __construct(
        private readonly MailboxReaderService $reader,
    ) {
    }




    public function list(
        User $user
    ): array {

        $pins =
            MailPinnedMessage::query()

                ->where(
                    'user_id',
                    $user->id
                )

                ->orderBy(
                    'position'
                )

                ->get();



        $messages = [];


        foreach ($pins as $pin) {

            $message =
                $this->reader->message(
                    $user->mailbox_address,
                    $pin->message_uid,
                    $pin->mailbox,
                );


            if (! $message) {
                continue;
            }



            $messages[] = [

                'id' =>
                    $pin->mailbox
                    . ':'
                    . $pin->message_uid,


                'mailbox' =>
                    $pin->mailbox,


                'uid' =>
                    (string) $pin->message_uid,


                'senderName' =>
                    $message['from']['name']
                    ??
                        $message['from']['address']
                        ??
                        '',


                'senderAddress' =>
                    $message['from']['address']
                    ??
                    '',


                'subject' =>
                    $message['subject']
                    ??
                    '(No subject)',


                'preview' =>
                    $message['preview']
                    ??
                    '',


                'receivedAt' =>
                    $message['date']
                    ??
                    null,


                'unread' =>
                    ! ($message['seen'] ?? false),


                'starred' =>
                    (bool) (
                        $message['starred']
                        ?? false
                    ),

            ];

        }


        return $messages;

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
