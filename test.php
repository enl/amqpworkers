<?php

require_once __DIR__.'/vendor/autoload.php';

use AmqpWorkers\Consumer;
use AmqpWorkers\Definition\Qos;
use AmqpWorkers\Definition\Queue;
use AmqpWorkers\Producer;
use AmqpWorkers\Worker\AbstractWorker;
use PhpAmqpLib\Connection\AMQPLazyConnection;

$worker = function($message) { echo $message; };
$formatter = function($payload) { return $payload; };

$connection = new AMQPLazyConnection('localhost', '5672', 'guest', 'guest');

$producer = Producer::factory($connection)
->withQueue(new Queue('test_produce_to'))
->withFormatter($formatter);

$consumer = Consumer::factory($connection)
->withQueue(Queue::factory('test_consume_from')->durable(true))
->withQos(Qos::factory()->count(10))
->withListener(AbstractWorker::fromClosure($worker))
->produceResult($producer);

$consumer->run();
