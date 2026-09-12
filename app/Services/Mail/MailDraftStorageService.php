<?php

namespace App\Services\Mail;

use Illuminate\Support\Facades\Process;
use RuntimeException;

class MailDraftStorageService
{
    private const READER =
        '/usr/local/bin/barmanasin-mail-reader';


    public function save(
        string $mailboxAddress,
        string $rawMessage,
    ): void {
        $mailboxAddress =
            $this->normalizeMailboxAddress(
                $mailboxAddress,
            );

        if (trim($rawMessage) === '') {
            throw new RuntimeException(
                'Cannot save an empty draft.',
            );
        }


        $result =
            Process::timeout(30)
                ->input($rawMessage)
                ->run([
                    '/usr/bin/sudo',
                    '-n',
                    self::READER,
                    'draft-save',
                    $mailboxAddress,
                ]);


        if ($result->failed()) {
            throw new RuntimeException(
                'Unable to save message to Drafts mailbox.',
            );
        }
    }


    private function normalizeMailboxAddress(
        string $mailboxAddress,
    ): string {
        $mailboxAddress =
            mb_strtolower(
                trim(
                    $mailboxAddress,
                ),
            );


        if (
            ! preg_match(
                '/^[a-z0-9._%+\-]+@barmanasin\.com$/',
                $mailboxAddress,
            )
        ) {
            throw new RuntimeException(
                'Invalid Barmanasin mailbox address.',
            );
        }


        return $mailboxAddress;
    }
}
