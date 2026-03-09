<?php

namespace Nietthijmen\LaravelPosthog\Events;

use Nietthijmen\LaravelPosthog\LaravelPosthog;

class LaravelPosthogEventHandler
{
    public function handleLogin(
        \Illuminate\Auth\Events\Login $event
    ) {
        $user = $event->user;
        LaravelPosthog::identify($user->getAuthIdentifier(), $user->toArray());
    }

}
