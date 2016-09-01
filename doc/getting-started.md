# Getting Started #

AMQP Workers is tiny layer of abstraction built on top of [php-amqplib](https://github.com/php-amqplib/php-amqplib). It provides more fluent and flexible interface and segregates amqp-related stuff and worker's business logic.

The main motivation to create this library is the fact that the original library is quite unconvenient to use because PHP does not have named function parameters.

## Installation ##

[PHP](http://php.net) 5.6+(just because this version is oldest officially supported) and [Composer](http://getcomposer.org) is required.

```bash
composer require enl/amqp-workers
```

Or you can add `"enl/amqp-workers": "dev-master"` to your `require` section:

```json
{
  "require": {
    "enl/amqp-workers": "dev-master"
  }
}
```

You have to use `dev-master` since library is not officially released yet. That is temporary state.

## Create your first Consumer ##

First of all we still need a connection:

```php
// I prefer lazy connections, it's up to you what actual connection class to use
$connection = AMQPLazyConnection('localhost', 5672, 'guest', 'guest');
```

Consumer consists of several tiny components:

* Worker implementation. This object MUST implement `__invoke` function and do whatever it needs to do with message from RabbitMQ.
* Queue definition. This object encapsulates parameters of `AMQPChannel::queue_declare()` function except `$worker`. 
* Qos definition, optional. If your worker should use QoS, you should set this object.

Let's assume that our worker should just echo message:

```php
class EchoWorker implements AmqpWorkers\Worker\WorkerInterface
{
    /**
     * @param string $message Yes, Consumer will unpack message's body for you.
     * @return mixed
     */
    public function __invoke($message)
    {
        echo $message;
    }
}
```

Ok, let's start all the things:

```php
$consumer = AmqpWorkers\Consumer::factory($connection)
    ->withWorker(new EchoWorker())
    ->withQueue(new Queue('test-queue')) // All other parameters are set to defaults
    ->run();
```

That's it!

## What's next? ##

You can learn more about [producers](producers.md) and [more advanced consumers](consumers.md) usage. 
