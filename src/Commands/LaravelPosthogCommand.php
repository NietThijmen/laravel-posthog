<?php

namespace Nietthijmen\LaravelPosthog\Commands;

use Illuminate\Console\Command;
use Nietthijmen\LaravelPosthog\LaravelPosthog;

class LaravelPosthogCommand extends Command
{
    public $signature = 'posthog:test';

    public $description = 'Send a test event to PostHog';

    public function handle(): int
    {
        try {
            LaravelPosthog::capture('CLI', 'Test Event', ['source' => 'LaravelPosthogCommand']);

            $this->info('Test event sent to PostHog successfully.');

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Failed to send test event to PostHog: '.$e->getMessage());

            return self::FAILURE;
        }
    }
}
