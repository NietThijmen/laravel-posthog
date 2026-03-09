<?php

namespace Nietthijmen\LaravelPosthog;

use Nietthijmen\LaravelPosthog\Commands\LaravelPosthogCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelPosthogServiceProvider extends PackageServiceProvider
{
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
            ->hasViews()
            ->hasMigration('create_laravel_posthog_table')
            ->hasCommand(LaravelPosthogCommand::class);
    }
}
