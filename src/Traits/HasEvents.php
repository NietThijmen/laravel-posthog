<?php

namespace Nietthijmen\LaravelPosthog\Traits;

use Illuminate\Contracts\Auth\Authenticatable;
use Nietthijmen\LaravelPosthog\LaravelPosthog;

trait HasEvents
{

    private function getAuthIdentifier()
    {
        if (!$this instanceof Authenticatable) {
            throw new \RuntimeException(
                static::class . ' must implement ' . Authenticatable::class
            );
        }

        return LaravelPosthog::getAuthIdentifier($this);
    }

    /**
     * Send an event to PostHog with the current model's identifier as the distinct ID.
     * @param string $event The name of the event to send
     * @param array<string,mixed> $properties Optional properties to include with the event
     * @return void
     */
    public function sendEvent(
        string $event,
        array $properties = [],
    ): void
    {
        LaravelPosthog::capture(
            $this->getAuthIdentifier(),
            $event,
            $properties
        );
    }

}
