<?php

return [
    'host' => env('POSTHOG_HOST', 'https://eu.i.posthog.com'),
    'api_key' => env('POSTHOG_PROJECT'),

    // Optional tracing settings, turn on and off based on your needs.
    'tracing' => [
        'automatic_auth_tracing' => env('POSTHOG_AUTOMATIC_AUTH_TRACING', false),
        'automatic_route_tracing' => env('POSTHOG_AUTOMATIC_ROUTE_TRACING', false),
    ],
];
