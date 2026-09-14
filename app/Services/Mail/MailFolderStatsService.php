<?php

namespace App\Services\Mail;

use RuntimeException;


class MailFolderStatsService
{

    public function __construct(
        private readonly MailboxReaderService $mailboxReader,
    ) {
    }


    /**
     * @return array<string, array{
     *     total:int,
     *     unread:int
     * }>
     */
    public function get(
        string $mailboxAddress,
    ): array {

        return [

            'inbox' =>
                $this->mailboxReader->folderCount(
                    $mailboxAddress,
                    'INBOX',
                ),


            'sent' =>
                $this->mailboxReader->folderCount(
                    $mailboxAddress,
                    'Sent',
                ),


            'drafts' =>
                $this->mailboxReader->folderCount(
                    $mailboxAddress,
                    'Drafts',
                ),


            'archive' =>
                $this->mailboxReader->folderCount(
                    $mailboxAddress,
                    'Archive',
                ),


            'trash' =>
                $this->mailboxReader->folderCount(
                    $mailboxAddress,
                    'Trash',
                ),


            'starred' =>
                $this->mailboxReader->starredCount(
                    $mailboxAddress,
                ),

        ];
    }

}
