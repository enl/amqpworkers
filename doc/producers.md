# Producers #

Producer is an object that sends something into AMQP exchange. It can format given messages somehow, has very stupid (for now) batch messages processing and can work with both queues and exchanges as a target.

Example to warm-up an interest:

```php
$producer = Producer::factory($connection)
    ->withExchange(Exchange::factory('test-exchange', 'fanout')->durable(true))
    ->withFormatter('json_encode');
    
$producer->produce(['field' => 'value']);
```
 
## Define target ##
 
Producer has two functions to define target where to send incoming messages:
 
* `Producer::withExchange(Definition\Exchange $exchange)` declares exchange;
* `Producer::withQueue(Definition\Queue $queue)` declares simple queue.

Quite simple, yeah?

Every definition has only a bit of mandatory parameters (`name` and exchange `type`), all others are preset into default parameters for `exchange_declare` and `queue_declare` AMQP functions.

So, the simplest definition of exchange looks like this:

```php
$exchange = new Definition\Exchange('test-exchange', 'fanout');
$queue = new Definition\Queue('test-queue');
```

Both classes support fluent interface for parameter setters and `factory` named constructor to avoid braces for `(new Queue('test'))->durable(true)`

## Format messages before sending them ##

As you can see from `Producer::produce($payload)` definition it accepts everything as a parameter. That's because `Producer` tries to format a message before creating `AMQPMessage` from given object.
 
Default formatter is as simple as this:

```php
function($payload) { return (string) $payload; }
```

But you can redefine its behavior with `Producer::withFormatter($formatter)` function.

```php
$producer = Producer::factory($connection)
    ->withFormatter('json_encode');
```

Yes, this function accepts everything [callable](http://php.net/manual/en/language.types.callable.php).

Please note, that formatters are not stacked, they are replaced by call of `withFormatter()`.

## Batch messaging ##

`Producer::produceAll($messages)` accepts `array|\Traversable` and for each `$message` calls `Producer::produce()`. Yeah, that is kinad stupid implementation, but we need something to start with. There is an [issue](https://github.com/enl/amqp-workers/issues/1) for this.
