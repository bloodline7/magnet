# Logger

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]
[![Build Status][ico-travis]][link-travis]
[![StyleCI][ico-styleci]][link-styleci]

This is where your description should go. Take a look at [contributing.md](contributing.md) to see a to do list.

## Installation

Via Composer

``` bash
$ composer require bloodline7/logger
```

## Usage



``` bash
$ composer require bloodline7/logger
```

```injectablephp
 'custom' => [
            'driver' => 'single',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'formatter' => Bloodline7\Logger\CustomizeFormatter::class,
        ],
```




## Change log

Please see the [changelog](changelog.md) for more information on what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [contributing.md](contributing.md) for details and a todolist.

## Security

If you discover any security related issues, please email author@email.com instead of using the issue tracker.

## Credits

- [Author Name][link-author]
- [All Contributors][link-contributors]

## License

MIT. Please see the [license file](license.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/bloodline7/logger.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/bloodline7/logger.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/bloodline7/logger/master.svg?style=flat-square
[ico-styleci]: https://styleci.io/repos/12345678/shield

[link-packagist]: https://packagist.org/packages/bloodline7/logger
[link-downloads]: https://packagist.org/packages/bloodline7/logger
[link-travis]: https://travis-ci.org/bloodline7/logger
[link-styleci]: https://styleci.io/repos/12345678
[link-author]: https://github.com/bloodline7
[link-contributors]: ../../contributors
