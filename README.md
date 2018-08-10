# Automatically alias and map the polymorphic types of Eloquent models

<a href="https://packagist.org/packages/sebastiaanluca/laravel-auto-morph-map"><img src="https://poser.pugx.org/sebastiaanluca/laravel-auto-morph-map/version" alt="Latest stable release"></img></a>
<a href="LICENSE.md"><img src="https://img.shields.io/badge/license-MIT-brightgreen.svg" alt="Software license"></img></a>
<a href="https://travis-ci.org/sebastiaanluca/laravel-auto-morph-map"><img src="https://img.shields.io/travis/sebastiaanluca/laravel-auto-morph-map/master.svg" alt="Build status"></img></a>
<a href="https://packagist.org/packages/sebastiaanluca/laravel-auto-morph-map"><img src="https://img.shields.io/packagist/dt/sebastiaanluca/laravel-auto-morph-map.svg" alt="Total downloads"></img></a>

<a href="https://blog.sebastiaanluca.com"><img src="https://img.shields.io/badge/link-blog-lightgrey.svg" alt="Read my blog"></img></a>
<a href="https://packagist.org/packages/sebastiaanluca"><img src="https://img.shields.io/badge/link-other_packages-lightgrey.svg" alt="View my other packages and projects"></img></a>
<a href="https://twitter.com/sebastiaanluca"><img src="https://img.shields.io/twitter/follow/sebastiaanluca.svg?style=social" alt="Follow @sebastiaanluca on Twitter"></img></a>
<a href="https://twitter.com/intent/tweet?text=Automatically%20alias%20and%20map%20the%20polymorphic%20types%20of%20Eloquent%20models.%20Via%20@sebastiaanluca%20https://github.com/sebastiaanluca/laravel-auto-morph-map"><img src="https://img.shields.io/twitter/url/http/shields.io.svg?style=social" alt="Share this package on Twitter"></img></a>

**Decouple your internal application namespace structure from your database by automatically aliasing and mapping your Eloquent models as short, uniform class names instead of full class namespaces.**

> By default, Laravel will use the fully qualified class name to store the type of the related model. For instance, given the example above where a `Comment` may belong to a `Post` or a `Video`, the default `commentable_type` would be either `App\Post` or `App\Video`, respectively. However, you may wish to decouple your database from your application's internal structure. In that case, you may define a relationship "morph map" to instruct Eloquent to use a custom name for each model instead of the class name.

See [Custom Polymorphic Types](https://laravel.com/docs/5.6/eloquent-relationships#polymorphic-relations) in the Laravel documentation for more information.

*Laravel auto morph map* improves upon that by scanning all your Eloquent models, automatically aliasing their base class names to uniform strings, and registering them as a polymorphic type. No more need for dozens of manual `Relation::morphMap()` calls to register model morph types!

## Table of contents

- [Requirements](#requirements)
- [How to install](#how-to-install)
- [How to use](#how-to-use)
    - [Defining model namespaces](#defining-model-namespaces)
    - [Overriding existing aliases](#overriding-existing-aliases)
    - [Caching morph types in production](#caching-morph-types-in-production)
    - [Configuration](#configuration)
        - [Casing](#casing)
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
composer require sebastiaanluca/laravel-auto-morph-map
```

## How to use

After installing this package, you're immediately good to go! The package will scan all your models and automatically register their polymorphic types on-the-fly.

Besides scanning and aliasing your models for you, this package alters no native Laravel functionality. Therefore, see the Laravel documentation on how to use [custom polymorphic types](https://laravel.com/docs/5.6/eloquent-relationships#polymorphic-relations).

If you wish to customize some behavior, read further.

### Defining model namespaces

*Laravel auto morph map* uses your `composer.json` PSR-4 autoload section to know which namespaces and paths to scan. In any new Laravel project, the default `App\\` namespace is already in place, so for most projects no additional setup required. If you have other namespaces registered like local modules or (dev) packages, those will be scanned too.

```json
{
    "autoload": {
        "psr-4": {
            "App\\": "app/",
            "MyModule\\": "modules/MyModule/",
            "MyPackage\\": "MyPackage/src/"
        }
    }
}
```

Furthermore it filters out traits, abstract classes, helper files, and other unusable items to only bind valid Eloquent models.

### Overriding existing aliases

If you wish, you can still override already aliased morph types in your service provider's `boot` method like you would normally:

```php
use Illuminate\Database\Eloquent\Relations\Relation;

Relation::morphMap([
    'posts' => \App\Post::class,
    'videos' => \App\Video::class,
]);
```

These will always have priority over the already defined ones.

### Caching morph types in production

To cache all aliases and speed up your application in production, add the cache command to your deploy scripts:

```
php artisan morphmap:cache
```

This scans all your current models and writes a static cache file to the `bootstrap/cache` directory. Upon subsequent framework booting, it reads the cache file instead of scanning and aliasing on-the-fly.

Note that this thus **disables runtime scanning**, meaning new models will not be recognized and changes to existing models will not be reflected (not very handy during development). You can however still change the case type in the configuration file, as the binding happens in a later stage.

To clear the cache file, run:

```
php artisan morphmap:clear
```

### Configuration

Run

```
php artisan vendor:publish
```

and select 

```
laravel-auto-morph-map (configuration)
```

to publish the configuration file.

#### Casing

By default, the case type for aliasing models is set to *snake case*. You can change this to use camel, snake, or studly casing.

See `\SebastiaanLuca\AutoMorphMap\Constants\CaseTypes` for possible options.

Snake case (default):

```php
Relation::morphMap([
    'collection_items' => 'App\CollectionItems',
]);
```

Camel case:

```php
Relation::morphMap([
    'collectionItems' => 'App\CollectionItems',
]);
```

Studly case:

```php
Relation::morphMap([
    'CollectionItems' => 'App\CollectionItems',
]);
```

The case type can still be changed after caching your models.

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

[link-packagist]: https://packagist.org/packages/sebastiaanluca/laravel-auto-morph-map
[link-travis]: https://travis-ci.org/sebastiaanluca/laravel-auto-morph-map
[link-contributors]: ../../contributors

[link-portfolio]: https://www.sebastiaanluca.com
[link-blog]: https://blog.sebastiaanluca.com
[link-packages]: https://packagist.org/packages/sebastiaanluca
[link-github-profile]: https://github.com/sebastiaanluca
[link-author-email]: mailto:hello@sebastiaanluca.com
