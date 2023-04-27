# Integrate the multitenancy single database in Laravel Nova.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/henryavila/laravel-nova-multitenancy.svg?style=flat-square)](https://packagist.org/packages/henryavila/laravel-nova-multitenancy)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/henryavila/laravel-nova-multitenancy/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/henryavila/laravel-nova-multitenancy/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/henryavila/laravel-nova-multitenancy/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/henryavila/laravel-nova-multitenancy/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/henryavila/laravel-nova-multitenancy.svg?style=flat-square)](https://packagist.org/packages/henryavila/laravel-nova-multitenancy)

Integrate the multitenancy single database in Laravel Nova.

This package is based on https://spatie.be/docs/laravel-multitenancy. So logic and config of spatie/laravel-multitenancy
still aplies

## v2.0.0
Another option to don't define the tenant to an route is set the following `default` data to route declaration
```php
Route::get('/', [Controller::class, 'index'])
		->defaults(\HenryAvila\LaravelNovaMultitenancy\LaravelNovaMultitenancy::SKIP_ROUTE, true);
```

## Installation

You can install the package via composer:

```bash
composer require henryavila/laravel-nova-multitenancy
```

You can publish the config, view and migrations with:

```bash
php artisan vendor:publish --provider="HenryAvila\LaravelNovaMultitenancy\LaravelNovaMultitenancyServiceProvider" 
```

The views file will be published, personalize it at your will.
Don't forget to run `npm run build`


You can run the migration with (To create the Tenant table)

```bash
php artisan migrate
```


Edit the `app\Http\Kernel.php` file adding an entry in web group and creating the tenant group

```php
protected $middlewareGroups = [
    // ...
    
    'web' => [
        // ...
        \HenryAvila\LaravelNovaMultitenancy\Http\Middleware\SetTenantMiddleware::class,
    ],
    
    // ...
    
    'tenant' => [
        \HenryAvila\LaravelNovaMultitenancy\Http\Middleware\NeedsTenant::class,
        \HenryAvila\LaravelNovaMultitenancy\Http\Middleware\EnsureValidTenantSession::class,
    ]
];
```

If you activated (User Impersonation)[https://nova.laravel.com/docs/4.0/customization/impersonation.html] 
on Laravel Nova, you must set up this event Listeners.

Edit the file `App\Providers\EventServiceProvider`

```php
 protected $listen = [
        \Laravel\Nova\Events\StartedImpersonating::class  => [
            \HenryAvila\LaravelNovaMultitenancy\Listeners\ClearTenantSessionListener::class,
        ],
        \Laravel\Nova\Events\StoppedImpersonating::class => [
            \HenryAvila\LaravelNovaMultitenancy\Listeners\ClearTenantSessionListener::class,
        ],
];
```

Add the Trait `HenryAvila\LaravelNovaMultitenancy\Traits\User` in your `User` model

If you whant to customize the Tenant model, change it in config file

Add the trait `\HenryAvila\LaravelNovaMultitenancy\Traits\ModelWithTenant` to all models that are tenant aware

Add to your `database` file a `tenant_connection` entry with the tenate db conneciton. See:
```php
return [
// database.php
    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for all database work. Of course
    | you may use many connections at once using the Database library.
    |
    */

    'default' => env('DB_CONNECTION', 'mysql'),

    'tenant_connection' => env('DB_TENANT_CONNECTION', 'tenant'), // <<== ADD THIS
```

## Usage

To protect an specift route, just add the 'tenant' middleware to route
```php
// in a routes file

Route::middleware('tenant')->group(function() {
    // routes
});
```


If you receite an error: `Route [login] not defined.`. 
Remember to change the route from `login` to `nova.login` in your `App\Http\MiddlewareAuthenticate\` file 

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Henry Ávila](https://github.com/henryavila)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
