# A lightweight package to split your code into contextual modules

<a href="https://packagist.org/packages/sebastiaanluca/laravel-module-loader"><img src="https://poser.pugx.org/sebastiaanluca/laravel-module-loader/version" alt="Latest stable release"></img></a>
<a href="LICENSE.md"><img src="https://img.shields.io/badge/license-MIT-brightgreen.svg" alt="Software license"></img></a>
<a href="https://travis-ci.org/sebastiaanluca/laravel-module-loader"><img src="https://img.shields.io/travis/sebastiaanluca/laravel-module-loader/master.svg" alt="Build status"></img></a>
<a href="https://packagist.org/packages/sebastiaanluca/laravel-module-loader"><img src="https://img.shields.io/packagist/dt/sebastiaanluca/laravel-module-loader.svg" alt="Total downloads"></img></a>

<a href="https://blog.sebastiaanluca.com"><img src="https://img.shields.io/badge/link-blog-lightgrey.svg" alt="Read my blog"></img></a>
<a href="https://packagist.org/packages/sebastiaanluca"><img src="https://img.shields.io/badge/link-other_packages-lightgrey.svg" alt="View my other packages and projects"></img></a>
<a href="https://twitter.com/sebastiaanluca"><img src="https://img.shields.io/twitter/follow/sebastiaanluca.svg?style=social" alt="Follow @sebastiaanluca on Twitter"></img></a>
<a href="https://twitter.com/intent/tweet?text=A%20lightweight%20Laravel%20package%20to%20split%20your%20code%20into%20individual%20modules.%20Via%20@sebastiaanluca%20https://github.com/sebastiaanluca/laravel-module-loader"><img src="https://img.shields.io/twitter/url/http/shields.io.svg?style=social" alt="Share this package on Twitter"></img></a>

**Laravel Module Loader helps you organize your project by splitting your domain code in contextual modules.**

By default, Laravel provides you with an `app/` directory to put all your classes in. In medium to large projects, you usually end up having a monolithic `app/` directory with tens or hundreds of directories and classes. To make *some* sense of that, they're typically organized per *type of class* (e.g. providers, services, models, …) too.

**An alternative** to that is organizing your code in groups that belong in the same context, i.e. modules. Since you usually develop one feature at a time, it makes it easier for you and other developers to find related code when working in that context.

Additionally, this lightweight module package provides some added benefits when making use of the include module service provider, including:

- individual configuration;
- publishable configuration;
- migrations;
- seeders;
- Eloquent factories;
- (PHP array or JSON) translations;
- views;
- Eloquent polymorphic mapping support;
- automatic event listener or subscriber registration;
- and automatic [router mapping](https://github.com/sebastiaanluca/laravel-router).

### Example

To give you a sense of how a modularized project is structured, take for instance a users, documents, and a shopping cart module:

```
User/
    database/
        factories/
        migrations/
        seeds/
    resources/
        lang/
        views/
    src/
        Commands/
        Jobs/
        Models/
        Providers/
            UserServiceProvider
    tests/
        UserTestCase

Document/
    resources/
        lang/
    src/
        Models/
        Providers/
            DocumentServiceProvider

ShoppingCart/
    src/
        Providers/
            ShoppingCartServiceProvider
```

## Table of contents

- [Requirements](#requirements)
- [How to install](#how-to-install)
- [Getting started](#getting-started)
    - [Creating a module](#creating-a-module)
    - [Caching in production environments](#caching-in-production-environments)
- [Going into detail](#going-into-detail)
    - [Scanning and registering modules](#scanning-and-registering-modules)
    - [Using a module service provider](#using-a-module-service-provider)
    - [Individual module configuration](#individual-module-configuration)
        - [Publishing a module's configuration](#publishing-a-modules-configuration)
    - [Using migrations](#using-migrations)
    - [Using factories](#using-factories)
    - [Using seeders](#using-seeders)
    - [Using translations](#using-translations)
    - [Using views](#using-views)
    - [Simplified polymorphic model type mapping](#simplified-polymorphic-model-type-mapping)
    - [Simplified event listener registration](#simplified-event-listener-registration)
    - [Automatic router mapping](#automatic-router-mapping)
    - [Automatic service provider registration](#automatic-service-provider-registration)
- [Package configuration](#package-configuration)
    - [Runtime autoloading](#runtime-autoloading)
    - [Module directories](#module-directories)
    - [Development environments](#development-environments)
- [License](#license)
- [Change log](#change-log)
- [Testing](#testing)
- [Contributing](#contributing)
- [Security](#security)
- [Credits](#credits)
- [About](#about)

## Requirements

- PHP 7.3 or higher
- Laravel 6.0 or higher

## How to install

Via Composer:

```bash
composer require sebastiaanluca/laravel-module-loader
```

## Getting started

There's only one required step to get started and that is to *create a module*.

### Creating a module

Say you want to create a module to group all your *user* related classes and files, run:

```
php artisan modules:create User
```

and the package will:

- scaffold your user module directories under your primary configured module directory;
- generate a correctly named (optional) service provider to fully enable the power of this package;
- and write its autoload information to your composer.json file.

Of course you can also do all of this manually. Create a `User` and `User/src` directory in any of your module directories and run `php artisan modules:refresh` afterwards to add it to your composer.json file (or do so manually too).

### Caching in production environments

*Optional*

To reduce the amount of files being read during application boot, you can opt to **cache a list of your module service providers** to improve load time. In addition to Composer providing a cached list of all your module classes, the package reads just the one cache file on boot and registers those instead of scanning all your module directories and loading them on-the-fly. Especially useful in production environments and advised to run during project deployment.

To cache all providers, run:

```
php artisan modules:cache
```

To clear the cache file, execute:

```
php artisan modules:clear
```

## Going into detail

### Scanning and registering modules

When you've manually created a new module, made some changes, added directories that need autoloading, and so on, you should refresh your modules:

```
php artisan modules:refresh
```

This will scan all your module directories for changes, write your modules' autoload configuration to composer.json (both classmap, PSR-4, and PSR-4 dev sections when applicable), and automatically update the Composer autoloader.

Your existing Composer configuration is not altered and no autoload entries are altered, unless:

- the module is missing;
- there are duplicate modules (by which one entry will be kept).

If you wish to keep all autoload entries for __modules that do not exist__, you can use the `--keep` option:

```
php artisan modules:refresh --keep
```

### Using a module service provider

*Optional*

A module should contain a service provider if you want your module to support:

- individual configuration;
- publishable configuration;
- migrations;
- seeders;
- Eloquent factories;
- (PHP array or JSON) translations;
- views;
- Eloquent polymorphic mapping support;
- automatic event listener or subscriber registration;
- and automatic [router mapping](https://github.com/sebastiaanluca/laravel-router).

When you create a module, a service provider is generated for you. If you want to create a provider yourself and have it automatically be registered on framework boot, create a class extending the correct base provider in your module's `src/Providers` directory.

Pay extra attention to the _studly cased_ naming of your provider, as it's only registered on boot if the class name starts with your module name.

```php
<?php

declare(strict_types=1);

namespace MyModule\Providers;

use SebastiaanLuca\Module\Providers\ModuleProvider;

class MyModuleServiceProvider extends ModuleProvider
{
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register() : void
    {
        parent::register();
    }

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot() : void
    {
        parent::boot();
    }
}
```

### Individual module configuration

*Optional; requires a [module service provider](#using-a-module-service-provider)*

Each module can contain a configuration file which you can use to group related settings. The snake-cased naming and location of the configuration file is important if you want it auto-loaded. Take for instance a `ShoppingCart` module:

```
modules/
    ShoppingCart/
        config/
            shopping-cart.php
        database/
        src/
```

The contents of the file are similar to any other Laravel configuration file and can contain anything you want:

```php
<?php

return [

    'setting' => 'value',

];
```

To retrieve a setting, call it like so:

```php
$setting = config('shopping-cart.setting')

// "value"
```

#### Publishing a module's configuration

*Optional; requires a [module service provider](#using-a-module-service-provider)*

If you don't want the configuration to reside in the module itself, you can either copy or move it to the root `/config` directory. Another option is to publish it like you would do for a package configuration file, i.e. let Laravel copy it for you:

```
php artisan vendor:publish
```

Then choose `* YourModule (configuration)` from the list.

Note that both configuration files will be merged ,but the one in the root `/config` directory will take precedence over the one in your module. If a same key is encountered in both files, the one from within your module will be ignored.

### Using migrations

*Optional; requires a [module service provider](#using-a-module-service-provider)*

Laravel Module Loader gives you 2 options when it comes to organizing migrations. Either you keep them in your default `/database/migrations` directory and maintain a chronological overview of all your migrations, or you store them contextually per module in e.g. `YourModule/database/migrations`.

Both locations are fine and interchangeable; i.e. you can combine both uses as they are sorted and executed by their date and time prefix.

### Using factories

*Optional; requires a [module service provider](#using-a-module-service-provider)*

Factories can be stored in your default `/database/factories` directory or per module in e.g. `YourModule/database/factories`. They are by default not namespaced and **only loaded in [development environments](#development-environments)** to prevent your application throwing errors when *autoload-dev* packages like Faker and so are are not installed on production systems.

### Using seeders

*Optional; requires a [module service provider](#using-a-module-service-provider)*

Seeders can be placed in your default `/database/seeds` directory or per module in `YourModule/database/seeds`. They are not namespaced and available globally, so watch out for identically named seeders across modules.

### Using translations

*Optional; requires a [module service provider](#using-a-module-service-provider)*

Translations are kept in the `/resources/lang` or `YourModule/resources/lang` module directory. If you use the latter and keep them within the module, remember to prefix your translation keys with the *snake cased* module name (as if you were using a package) to retrieve the correct value:

```php
@lang('your-module::dashboard.intro')

@lang('your-module::auth/password_reset.label')
```

### Using views

*Optional; requires a [module service provider](#using-a-module-service-provider)*

As with translations, views follow the same pattern. You can keep them in the default `/resources/views` directory or in `YourModule/resources/views`. To use a view or include a partial, prefix the path with your *snake cased* module name:

```php
view('my-module::dashboard')

@include('my-module::pages.auth.password_reset')

@include('my-module::pages/welcome')
```

### Simplified polymorphic model type mapping

*Optional; requires a [module service provider](#using-a-module-service-provider)*

Instead of manually calling `Relation::morphMap([])`, you can map the polymorphic types or aliases of your Eloquent models by defining a morph map array in your module service provider:

```php
<?php

declare(strict_types=1);

namespace MyModule\Providers;

use MyModule\Models\Item;
use MyModule\Models\ShoppingCart;
use SebastiaanLuca\Module\Providers\ModuleProvider;

class MyModuleServiceProvider extends ModuleProvider
{
    /**
     * The polymorphic models to map to their alias.
     *
     * @var array
     */
    protected $morphMap = [
        'item' => Item::class,
        'shopping_cart' => ShoppingCart::class,
    ];
}
```

Be sure to check out my other [auto morph map](https://github.com/sebastiaanluca/laravel-auto-morph-map) package to **automatically alias all your models** without writing any code.

### Simplified event listener registration

*Optional; requires a [module service provider](#using-a-module-service-provider)*

In the same way you can define a morph map in your module service provider, you can also define a list of event listeners or subscribers:

```php
<?php

declare(strict_types=1);

namespace MyModule\Providers;

use MyModule\Listeners\UpdateUserInfo;
use MyModule\Listeners\UserEventSubscriber;
use SebastiaanLuca\Module\Providers\ModuleProvider;
use Users\Events\UserCreated;

class MyModuleServiceProvider extends ModuleProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        UserCreated::class => [
            UpdateUserInfo::class,
        ]
    ];

    /**
     * The subscriber classes to register.
     *
     * @var array
     */
    protected $subscribe = [
        UserEventSubscriber::class,
    ];
}
```

### Automatic router mapping

*Optional; requires a [module service provider](#using-a-module-service-provider)*

Handling and organizing routes in a medium or large Laravel application can get messy quite fast. To counter that ever-expanding list of routes, this package provides support for [sebastiaanluca/laravel-router](https://github.com/sebastiaanluca/laravel-router) to automatically register and map your contextual routes and routers:

```php
<?php

declare(strict_types=1);

namespace MyModule\Providers;

use MyModule\Http\Routers\UserAuthRouter;
use MyModule\Http\Routers\UserDashboardRouter;
use SebastiaanLuca\Module\Providers\ModuleProvider;

class MyModuleServiceProvider extends ModuleProvider
{
    /**
     * The routers to be automatically mapped.
     *
     * @var array
     */
    protected $routers = [
        UserAuthRouter::class,
        UserDashboardRouter::class,
    ];
}
```

### Automatic service provider registration

*Optional; requires a [module service provider](#using-a-module-service-provider)*

To counter a module's service provider from getting out of control in terms of lines of code and methods, it might be best to **split it into multiple other service providers** that each have a single task. For instance, a `ModuleEventProvider` can be used to map all events of the module, while another `ModuleRouteProvider` can contain all the routes or routers to easily map upon application boot.

Of course those providers will still need to be registered. You can do so manually in the module's default provider, but also automatically using a list:

```php
<?php

declare(strict_types=1);

namespace MyModule\Providers;

use MyModule\Providers\ModuleEventProvider;
use MyModule\Providers\ModuleRouteProvider;
use SebastiaanLuca\Module\Providers\ModuleProvider;

class MyModuleServiceProvider extends ModuleProvider
{
    /**
     * The additional providers to register.
     *
     * @var array
     */
    protected $providers = [
        ModuleEventProvider::class,
        ModuleRouteProvider::class,
    ];
}
```

## Package configuration

To copy the package's configuration file to your root config directory, run:

```
php artisan vendor:publish
```

Then choose `laravel-module-loader (configuration)` from the list.

### Runtime autoloading

This package supports runtime autoloading of all modules and their non-namespaced database directories. Basically it reads and loads your modules during framework boot, instead of relying on Composer to autoload your module's classes before.

Set `runtime_autoloading` in the package's configuration to `true` and remove your module entries from composer.json's autoload sections.

Note that there are some trade-offs to enabling this:

- You cannot generate an authorative classmap using `composer dumpautoload -a` (usually in production);
- it's a bit slower (yet hardly noticeable), but you don't have to keep your composer.json updated;
- PHPStorm (and perhaps other IDEs) don't automatically tag your modules and their tests as namespaced sources.

### Module directories

By default, only the `modules` directory in your root project is scanned for modules. By altering or extending this list, you can further organize the directory or directories your modules reside in however you like.

For instance you can group personal and work-related modules in their separate folders for reusability across projects.

### Development environments

The *development environments* configuration option allows you to change the list of environments…

- factories should be loaded in;
- and tests are autoloaded in (if [runtime autoloading](#runtime-autoloading) is enabled).

The app environment is read from your `.env` file through the Laravel application config.

## License

This package operates under the MIT License (MIT). Please see [LICENSE](LICENSE.md) for more information.

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

```bash
composer install
composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CODE OF CONDUCT](CODE_OF_CONDUCT.md) for details.

## Security

If you discover any security related issues, please email [hello@sebastiaanluca.com][link-author-email] instead of using the issue tracker.

## Credits

- [Sebastiaan Luca][link-github-profile]
- [All Contributors][link-contributors]

## About

My name is Sebastiaan and I'm a freelance back-end developer specializing in building custom Laravel applications. Check out my [portfolio][link-portfolio] for more information, [my blog][link-blog] for the latest tips and tricks, and my other [packages][link-packages] to kick-start your next project.

Have a project that could use some guidance? Send me an e-mail at [hello@sebastiaanluca.com][link-author-email]!

[link-packagist]: https://packagist.org/packages/sebastiaanluca/laravel-module-loader
[link-travis]: https://travis-ci.org/sebastiaanluca/laravel-module-loader
[link-contributors]: ../../contributors

[link-portfolio]: https://www.sebastiaanluca.com
[link-blog]: https://blog.sebastiaanluca.com
[link-packages]: https://packagist.org/packages/sebastiaanluca
[link-github-profile]: https://github.com/sebastiaanluca
[link-author-email]: mailto:hello@sebastiaanluca.com
