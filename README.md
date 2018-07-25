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

An alternative to that is organizing your code in groups that belong in the same context, i.e. modules. Since you usually develop one feature at a time, it makes it easier for you and other developers to find related code when working in that context. For instance, grouping everything for users, documents, and a shopping cart under a `User`, `Document`, and `ShoppingCart` module respectively:

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
- [How to use](#how-to-use)
    - [Creating a module](#creating-a-module)
- [License](#license)
- [Change log](#change-log)
- [Testing](#testing)
- [Contributing](#contributing)
- [Security](#security)
- [Credits](#credits)
- [About](#about)

## Requirements

- PHP 7.2 or higher
- Laravel 5.6 or higher

## How to install

Via Composer:

```bash
composer require sebastiaanluca/laravel-module-loader
```

## How to use

### Creating a module

- Manually for now
- Module directory
- Only requirement is a `src/Providers` directory and a service provider with correct naming
- Execute command to update composer.json autoload section (link to config option to enable runtime autoloading)

### Individual module configuration

_Optional_

By default, each module can contain a configuration file. You can use this group related settings.

The name of the configuration file is important if you want it auto-loaded. Take for instance a `ShoppingCart` module:

```
modules/
    ShoppingCart/
        config/
            shopping-cart.php
```

#### Publishing a module's configuration

If you don't want the configuration to reside in the module itself, you can either copy or move it to the root `/config` directory. Another option is to publish it like you would do for a package configuration file (i.e. let Laravel copy it for you):

```php
php artisan vendor:publish
```

Then choose `* YourModule (configuration)` from the list.

Note that configuration files in the root `/config` directory take precedence over the ones in your module. If a same key is encountered in both files, the one from within your module will be omitted.

### Migrations

_Optional_

- In /database/migrations (keep an overview) or /modules/YourModule/database/migrations (keep in context)
- Autoloaded by this package
- Module migrations combined with app migrations based on prefixed date and time

### Factories

_Optional_

- In /database/factories or /modules/YourModule/database/factories
- Both directories get scanned
- Autoloaded by this package if your module has a `/modules/YourModule/database/factories` directory

### Seeders

_Optional_

- In /database/seeds or /modules/YourModule/database/seeds
- Not namespaced
- No special handling in this package, manual usage
- Called in DatabaseSeeder or with the `--class` CLI option

### Translations

_Optional_

- In /resources/lang or /modules/YourModule/resources/lang
- Prefixed with the module name (converted to _snake\_case_): `@lang('your-module::subdirectory/file.key')` (subdirectory optional, provided as example)

### Views

_Optional_

- In /resources/views or /modules/YourModule/resources/views
- Prefixed with the module name (converted to _snake\_case_): `view('your-module::subdirectory.view')` (subdirectory optional, provided as example)

## Configuration

### Runtime autoloading

This package supports runtime autoloading of all modules and their non-namespaced database directories. A bit slower, but you don't have to keep your composer.json updated.

Enable `runtime_autoloading` in the package's configuration and remove your module entries from composer.json's autoload sections.

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
