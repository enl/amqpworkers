<?php


//namespace AmqpWorkers\Tests;
//
//
//use PhpAmqpLib\Connection\AMQPSocketConnection;
//
//class ConsumerTest
//{
//
//}
//
use AmqpWorkers\Consumer;
use AmqpWorkers\Definition\Qos;
use AmqpWorkers\Definition\Queue;
use AmqpWorkers\Producer;
use PhpAmqpLib\Connection\AMQPSocketConnection;

$worker = function($message) { echo $message; };
$formatter = function($payload) { return $payload; };

$connection = new AMQPSocketConnection();
//
$producer = Producer::factory($connection)
    ->toExchange('test_produce_to', Configuration::factory())
    ->withFormatter($formatter);
//
$consumer = Consumer::factory($connection)
    ->withQueue(Queue::factory('test_consume_from')->durable(true))
    ->withQos(Qos::factory()->size(10))
    ->withListener($worker)
    ->produceResult($producer);
//
//$consumer->run();
