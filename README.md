# AMQP Workers library

All this library does is providing more fluent experience with AMQP. Original phpamqplib has very strong approach to its functions declarations.

I decided to create a tiny layer of abstraction which provides a bit more flexible interface:

```php
$consumer = Consumer::factory($connection)
    ->withQueue(new Queue('consume_from'))
    ->withWorker($worker)
    ->run();
```

That is very short basic setup, `Consumer` has much more functions. Read [the manual](doc/index.md) for more detailed description.

### Features ###

* More fluent and flexible interface than original [phpamqplib] library;
* Very lazy approach, amqp-related stuff is called only when you call `run` or `produce` functions.

## Install ##

```
composer require enl/amqp-workers
```

## Usage ##

```php

// Create AMQPConnection
$connection = new AMQPLazyConnection();

// We're using static `factory` function only for convenience with fluent interface
$consumer = Consumer::factory($connection)
    // set queue definition. all parameters are default
    ->withQueue(new Queue('consume_from'))
    // Worker is an object of WorkerInterface which handles given message body
    ->withWorker($worker)
    // declare and start consuming the queue
    ->run();
```
## Change log ##

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing ##

```
composer test
```

## Contributing ##

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CONDUCT](CONDUCT.md) for details.

## Security ##

If you discover any security related issues, please email [deadyaga@gmail.com](mailto:deadyaga@gmail.com) instead of using the issue tracker.

## Credits

- [:author_name][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/:vendor/:package_name.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/:vendor/:package_name/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/:vendor/:package_name.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/:vendor/:package_name.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/:vendor/:package_name.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/:vendor/:package_name
[link-travis]: https://travis-ci.org/:vendor/:package_name
[link-scrutinizer]: https://scrutinizer-ci.com/g/:vendor/:package_name/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/:vendor/:package_name
[link-downloads]: https://packagist.org/packages/:vendor/:package_name
[link-author]: https://github.com/:author_username
