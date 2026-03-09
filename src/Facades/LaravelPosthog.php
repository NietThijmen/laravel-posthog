<?php

namespace Nietthijmen\LaravelPosthog\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Nietthijmen\LaravelPosthog\LaravelPosthog
 */
class LaravelPosthog extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Nietthijmen\LaravelPosthog\LaravelPosthog::class;
    }
}
