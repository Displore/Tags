# Displore Tags

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Quality Score][ico-code-quality]][link-code-quality]

Basic tags for Laravel.

## Install

### Via [Displore Core][link-displore-core]

``` bash
$ php artisan displore:install tags
```
This does everything for you, from the Composer requirement to the addition of Laravel service providers.

### Via Composer

``` bash
$ composer require displore/tags
```
This requires the addition of the Tags service provider and Tags facade alias to config/app.php.
`Displore\Tags\TagsServiceProvider::class,`
and
`Displore\Tags\Facades\Tagger::class,`

### Configuration

Run the following command to get the migrations.
```bash
$ php artisan vendor:publish --tag=displore.tags.migrations
```

## Usage

Tags can be added to every model. They optionally have a description and a category. The Tagger class is resolved by Laravel.
In order to make it work, your model(s) should use the trait `Displore\Tags\Taggable`.
Some examples:
```php
$task = Task::find(1);
Tagger::tag($task, 'Important');
// or...
$task->tag('Important');
Tagger::create('Important', 'Optional category', 'optional description');
Tagger::tagOrCreate($task, 'Very Important');
Tagger::untag($task, 'Important');
// or...
$task->untag('Important');
Tagger::syncTags($task, ['Important', 'Very Important']);
// or...
$task->syncTags(['Important', 'Very Important']);
$tagsForTask = $task->tags;
```

## Change log

Please see [changelog](changelog.md) for more information what has changed recently.

## Testing

The package comes with unit tests.
In a Laravel application, with [Laravel Packager](https://github.com/Jeroen-G/laravel-packager):
``` bash
$ php artisan packager:git *Displore Github url*
$ php artisan packager:tests Displore Tags
$ phpunit
```

## Contributing

Please see [contributing](contributing.md) for details.

## Credits

- [JeroenG][link-author]
- [All Contributors][link-contributors]

## License

The EUPL License. Please see the [License File](license.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/displore/tags.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/displore/tags.svg?style=flat-square

[link-displore-core]: https://github.com/displore/core

[link-packagist]: https://packagist.org/packages/displore/tags
[link-code-quality]: https://scrutinizer-ci.com/g/displore/tags
[link-author]: https://github.com/Jeroen-G
[link-contributors]: ../../contributors
