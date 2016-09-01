# Consumers #
 
As we remember from [getting started guide](getting-started.md), Consumer is an object which connects to an AMQP queue, retrieves messages and sends them to a Worker.
 
Let's see how to use this class in more detailed way.

```php
Consumer::factory($connection)
    ->withQueue(new Queue('name'))
    ->withQos(Qos::factory()->count(10))
    ->withWorker($worker)
    ->produceResult($producer)
    ->run();
```

## Define Queue ##

`Definition\Queue` is an object that encapsulates all parameters for `queue_declare` function. By default, you need to pass only `name`. All other parameters already have values exactly matching `queue_declare` defaults.
  
```php
// Pass name and make queue durable
$queue = Queue::factory('name')->durable(true);
$consumer->withQueue($queue);
```

## Define QoS ##

First of all, here it is [official doc](https://www.rabbitmq.com/consumer-prefetch.html) about QoS in amqp and RabbitMQ. So, if you define QoS for Consumer it will grab only limited amount of unacknowledged messages from queue.

```php
$qos = Qos::factory()->count(10)->global(true);
$consumer->withQos($qos);
```

*Note: It seems that php-amqplib does not support `prefetch_size` parameter.*

## Define Worker ##

What is Workser? Worker is everything that implements `WorkerInterface`:

```php
interface WorkerInterface 
{
    /**
     * @param string $message
     * @return mixed
     */
    public function __invoke($message);
}
```

Yes, that simple. This is because, actually, workers are out of the library's scope. They are packed with _your own business logic_. So I try to influence on our code as less as possible.

### Tips and conventions ###

* Consumer WILL unpack `AMQPMessage` for you and give you only payload. That's all your business logic actually needs ;)
* If Worker returns something which is casted to `true` AND you defined a producer, consumer WILL try to produce the result with given producer;
* Consumer will acknowledge the message regardless of workers result if it does not throw exception or raise an error. Why? See next advice :) 
* [Let it crash](http://c2.com/cgi/wiki?LetItCrash)! If something went wrong, let it crash, throw exception or raise error. It's ok. What you really need is to simply die, return everything to queue (amqp does it by default with unacknowledged messages) and _restart dead script_, not try to restore all the things on-the-fly;
* Do not try to solve all the problems inside one worker, you should segregate responsibilites and create as many workers as many responsibilities you have.

### ClosureWorker ###

If your worker is very simple, you can use `ClosureWorker` class to define it:

```php
$worker = new ClosureWorker(function($message) {
    echo $message;
});
$consumer->withWorker($worker);
```

## Send worker's result to Producer ##

What if I need to send to AMQP the result of worker into another exchange? That is exactly what `produceResult($producer)` function does.

```php
$consumer = Consumer::factory($connection)->produceResult($producer);
```

This function will set given producer. This producer will be called with whatever Worker returned after message is handled.

*Note: Worker MUST return something which is not false (or looks like false) if you want to produce it.*

*Note: Consumed message will be acknowledged AFTER the result is successfully produced.*


## Run! ##

The Consumer is super-lazy, it does anything only when you tell him to `run`. 

```php
// Define you consumer here
$consumer = Consumer::factory($connection);
$consumer->run(); // All amqp-related stuff is executed in this function.
```
