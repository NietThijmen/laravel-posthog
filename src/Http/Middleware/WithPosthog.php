<?php

namespace Nietthijmen\LaravelPosthog\Http\Middleware;

use Illuminate\Support\Str;

/**
 * Implements some extra PostHog functionality.
 * - If the session doesn't have a 'posthog_distinct_id', it generates a new UUID and stores it in the session.
 * TODO: Look into tracking pageviews with a config for auto-capture
 */
class WithPosthog
{
    public function handle($request, \Closure $next)
    {
        if (!$request->session()->has('posthog_distinct_id')) {
            $request->session()->put('posthog_distinct_id', Str::uuid()->toString());
        }

        return $next($request);

    }

}
