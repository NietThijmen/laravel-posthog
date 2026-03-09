<?php

namespace Nietthijmen\LaravelPosthog\Traits;

use Nietthijmen\LaravelPosthog\LaravelPosthog;

trait HasEvents
{

    private function getAuthIdentifier()
    {
        if(property_exists($this, 'getAuthIdentifier')) {
            return $this->getAuthIdentifier();
        }

        if(property_exists($this, 'id')) {
            return $this->id;
        }

        return null;
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
