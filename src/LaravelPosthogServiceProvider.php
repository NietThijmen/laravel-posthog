<?php

namespace Nietthijmen\LaravelPosthog;

use Laravel\Pennant\Feature;
use Laravel\Pennant\PennantServiceProvider;
use Nietthijmen\LaravelPosthog\Commands\LaravelPosthogCommand;
use Nietthijmen\LaravelPosthog\Events\LaravelPosthogEventHandler;
use PostHog\PostHog;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelPosthogServiceProvider extends PackageServiceProvider
{
    protected array $events = [
        'Illuminate\Auth\Events\Login' => [
            'handleLogin',
        ],
    ];

    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-posthog')
            ->hasConfigFile()
            ->hasCommand(LaravelPosthogCommand::class)
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->setName('posthog:install')
                    ->setDescription('Install the Laravel PostHog package')
                    ->publishConfigFile()
                    ->askToStarRepoOnGitHub('Nietthijmen/laravel-posthog');
            });
    }

    private function initialisePostHog(): void
    {
        try {
            PostHog::init(
                config('posthog.api_key'),
                [
                    'host' => config('posthog.host'),
                ]
            );
        } catch (\Exception $exception) {
            // don't throw an error as the user might still be setting up
        }
    }

    private function maybeInitialisePennant()
    {
        if(class_exists("Laravel\Pennant\Feature")) {
            Feature::extend(
                'posthog',
                function ($feature) {
                    return new PostHogPennantDriver();
                }
            );
        }

    }

    /**
     * Initialise the underlying PostHog SDK
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
        $this->initialisePostHog();
        $this->maybeInitialisePennant();

    }

    public function register()
    {
        parent::register();

        foreach ($this->events as $event => $handlers) {
            foreach ($handlers as $handler) {
                \Event::listen($event, [LaravelPosthogEventHandler::class, $handler]);
            }
        }

    }
}
