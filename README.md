# Laravel PostHog

[![Latest Version on Packagist](https://img.shields.io/packagist/v/nietthijmen/laravel-posthog.svg?style=flat-square)](https://packagist.org/packages/nietthijmen/laravel-posthog)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/nietthijmen/laravel-posthog/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/nietthijmen/laravel-posthog/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/nietthijmen/laravel-posthog/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/nietthijmen/laravel-posthog/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/nietthijmen/laravel-posthog.svg?style=flat-square)](https://packagist.org/packages/nietthijmen/laravel-posthog)

Integrate PostHog product analytics into your Laravel application.
We can handle exception handling, pushing events from your user and more.


> This package is still in its early development, so expect some breaking changes in the future.
> If you want to contribute, please feel free to open a pull request or an issue.
## Installation

You can install the package via composer:

```bash
composer require nietthijmen/laravel-posthog
```

You can then install all package parts using
```bash 
php artisan posthog:install
```
## Usage
## Events
You can use the package to push events to PostHog. For example, you can push an event when a user logs in:

```php
use NietThijmen\LaravelPostHog\LaravelPosthog;
LaravelPosthog::capture(
    distinctId: LaravelPosthog::getAuthIdentifier(),
    event: 'User Logged In',
    properties: [
        'email' => auth()->user()->email,
    ]
);
```

There is a shorthand trait for this as well, which you can use in your User model:

```php
use NietThijmen\LaravelPostHog\Traits\HasEvents;
class User extends Authenticatable
{
    use HasEvents;
}
```

Then you can push events like this:

```php
use App\Models\User;
$user = User::find(1);
$user->sendEvent(
    event: 'User Logged In',
    properties: [
        'email' => $user->email,
    ]
);
```
## Exception Handling
You can also use the package to handle exceptions and push them to PostHog. 
To handle application exception you can use our `CaptureExceptions` Class inside your `bootstrap/app.php` file:
```php
use Nietthijmen\LaravelPosthog\Helpers\CaptureExceptions;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        //...
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //...
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        CaptureExceptions::captureExceptions($exceptions);
    })->create();
```

You can also manually capture exceptions and push them to PostHog:

```php
use Nietthijmen\LaravelPosthog\LaravelPosthog;
LaravelPosthog::captureException(
    new \Exception("Something went wrong!")
);
```
## Feature Flags
This package fully integrates with [Pennant](https://laravel.com/docs/12.x/pennant) to provide feature flag support. You can use the `posthog` driver to fetch feature flags from PostHog.
To use the `posthog` driver, you need to add it to your `config/pennant.php` configuration file:

```php
    'default' => env('PENNANT_STORE', 'posthog'),
    'stores' => [
        'posthog' => [
            'driver' => 'posthog',
        ],
    ],
```

Then you can use the `posthog` driver to check if a feature flag is enabled:

```php
use Laravel\Pennant\Feature;
$is_active = Feature::active("My-Test-Feature");

dd($is_active ? "Feature is active" : "Feature is not active");
```
## Laravel/AI tracing
The package also integrates with [Laravel/AI](https://laravel.com/docs/12.x/ai-sdk) to provide tracing support for your AI interactions.
This tracing is done fully automatically and transparent to your AI interactions, so you don't have to do anything to enable it.
## Middleware
This package has a middleware that should be added to your `bootstrap/app.php` file to automatically capture the authenticated user's distinct id and push it to PostHog:

```php
use Nietthijmen\LaravelPosthog\Http\Middleware\WithPosthog;
use \Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        //...
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            WithPosthog::class,
        ])
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        
    })->create();
```

## Commands
The package also has 2 commands, `install` and `test`
The `install` command will publish the configuration file:

```bash
php artisan posthog:install
```

The `test` command will send a test event to PostHog to verify that the integration is working correctly:

```bash
php artisan posthog:test
```

## Credits

- [NietThijmen](https://github.com/NietThijmen)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
