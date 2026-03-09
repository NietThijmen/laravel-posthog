<?php

namespace Nietthijmen\LaravelPosthog;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Spatie\Backtrace\Backtrace;

/**
 * A wrapper around most of the PostHog API, but with error handling and some shorthand functions.
 */
class LaravelPosthog
{
    /**
     * Get the unique identifier for the current user, or a random UUID if no user is authenticated.
     *
     * @return string
     */
    public static function getAuthIdentifier(): mixed
    {
        if (Auth::user()) {
            return Auth::user()->getAuthIdentifier();
        }

        return Str::uuid()->toString();
    }

    /**
     * Capture an event with PostHog
     *
     * @param  string  $distinctId  Unique identifier for the user or entity
     * @param  string  $event  Name of the event to capture
     * @param  array  $properties  Optional properties to include with the event
     */
    public static function capture(string $distinctId, string $event, array $properties = []): void
    {
        try {
            \PostHog\PostHog::capture([
                'distinctId' => $distinctId,
                'event' => $event,
                'properties' => $properties,
            ]);
        } catch (\Exception $e) {
            // Log the error or handle it as needed, but don't throw an exception
            \Log::error("Failed to capture event '{$event}' for distinctId '{$distinctId}': ".$e->getMessage());
        }
    }

    /**
     * Identify a user with PostHog (should be called on sign-in, our automatic capture should do this for us)
     *
     * @param  string  $distinctId  Unique identifier for the user
     * @param  array  $properties  Optional properties to associate with the user
     */
    public static function identify(string $distinctId, array $properties = []): void
    {
        try {
            \PostHog\PostHog::identify([
                'distinctId' => $distinctId,
                'properties' => $properties,
            ]);
        } catch (\Exception $e) {
            // Log the error or handle it as needed, but don't throw an exception
            \Log::error("Failed to identify user with distinctId '{$distinctId}': ".$e->getMessage());
        }
    }

    /**
     * Listen to exceptions and capture them as events in PostHog
     */
    public static function captureException(
        \Throwable $exception,
        bool $isHandled = false
    ): void {

        $backtrace = Backtrace::create();
        $url = request()->fullUrl();

        self::capture(
            self::getAuthIdentifier(),
            '$exception',
            [
                '$exception_fingerprint' => $exception->getMessage().' at '.$exception->getFile().':'.$exception->getLine(),
                '$exception_level' => 'error', // Exception level is not standard in PHP.
                '$current_url' => $url,
                '$exception_list' => [
                    [
                        'type' => get_class($exception),
                        'value' => $exception->getMessage(),
                        'mechanism' => [
                            'synthetic' => false,
                            'handled' => $isHandled,
                        ],
                        'stacktrace' => [
                            'type' => 'raw',
                            'frames' => array_map(function ($frame) {
                                return [
                                    // TODO: Wait for posthog to allow PHP as a language in stacktraces, for now we just set it to custom
                                    'platform' => 'custom',
                                    'lang' => 'custom',
                                    'filename' => $frame->file,
                                    'lineno' => $frame->lineNumber,
                                    'function' => $frame->method,
                                ];
                            }, $backtrace->frames()),
                        ],
                    ],
                ],
            ]
        );

    }
}
