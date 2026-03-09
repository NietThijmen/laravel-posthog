<?php

namespace Nietthijmen\LaravelPosthog\Commands;

use Illuminate\Console\Command;

class LaravelPosthogCommand extends Command
{
    public $signature = 'laravel-posthog';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
