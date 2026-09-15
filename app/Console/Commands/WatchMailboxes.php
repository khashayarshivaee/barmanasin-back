<?php

namespace App\Console\Commands;

use App\Services\Mail\MailWatcherService;
use Illuminate\Console\Command;


class WatchMailboxes extends Command
{
    protected $signature = 'mail:watch';


    protected $description = 'Watch mailboxes for incoming messages';


    public function __construct(
        private readonly MailWatcherService $watcher,
    ) {
        parent::__construct();
    }


    public function handle(): int
    {
        $this->info(
            'Mail watcher started.'
        );


        while (true) {

            try {

                $this->watcher->scan();

            } catch (\Throwable $exception) {

                report($exception);

                $this->error(
                    $exception->getMessage()
                );
            }


            sleep(5);
        }


        return self::SUCCESS;
    }
}
