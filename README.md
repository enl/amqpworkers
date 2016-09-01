# AMQP Workers library

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status](https://travis-ci.org/enl/amqpworkers.svg?branch=master)](https://travis-ci.org/enl/amqpworkers)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/enl/amqpworkers/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/enl/amqpworkers/?branch=master)
[![Total Downloads][ico-downloads]][link-downloads]

All this library does is providing more fluent experience with AMQP. Original phpamqplib has very strange approach to its functions declarations.

I decided to create a tiny layer of abstraction which provides a bit more flexible interface:

```php
$consumer = Consumer::factory($connection)
    ->withQueue(new Queue('consume_from'))
    ->withWorker($worker)
    ->run();
```

That is very short basic setup, `Consumer` has much more functions. Read [the manual](doc/getting-started.md) for more detailed description.

### Features ###

* More fluent and flexible interface than original [php-amqplib](https://github.com/php-amqplib/php-amqplib) library;
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

- [Alex Panshin][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/enl/amqp-workers.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/enl/amqp-workers.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/enl/amqp-workers
[link-downloads]: https://packagist.org/packages/enl/amqp-workers
[link-author]: https://github.com/enl
[link-contributors]: https://github.com/enl/amqpworkers/graphs/contributors
