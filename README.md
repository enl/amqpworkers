# AMQP Workers library

All this library does is providing more fluent experience with AMQP. Original phpamqplib has very strang approach to its functions declarations.

I decided to create a tiny layer of abstraction which provides a bit more flexible interface:

```php
$worker = function($message) { echo $message; };
$formatter = function($payload) { return $payload; };

$connection = new AMQPLazyConnection('localhost', '5672', 'guest', 'guest');

$producer = Producer::factory($connection)
    // set exchange definition
    ->withExchange(Exchange::factory('test_produce_to', 'fanout'))
    // set formatter which will be called before sending a message to rabbitmq
    ->withFormatter($formatter);

$consumer = Consumer::factory($connection)
    // set queue definition
    ->withQueue(Queue::factory('test_consume_from')->durable(true))
    // set QoS if you want
    ->withQos(Qos::factory()->count(10))
    // set actual worker instance which will handle messages
    ->withListener($worker)
    // If worker returns something can you please produce it here?
    ->produceResult($producer);

$consumer->run();
```

Actually, basic setup is much shorter:

```php
$consumer = Consumer::factory($connection)
    ->withQueue(new Queue('consume_from'))
    ->withListener($worker)
    ->run();
```
