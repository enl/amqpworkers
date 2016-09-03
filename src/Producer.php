<?php


namespace AmqpWorkers;

use AmqpWorkers\Definition\Exchange;
use AmqpWorkers\Definition\Queue;
use AmqpWorkers\Exception\ProducerNotProperlyConfigured;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AbstractConnection;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * Producer is an object that sends something into AMQP exchange.
 * It can format given messages somehow,
 * has very stupid (for now) batch messages processing
 * and can work with both queues and exchanges as a target.
 *
 * @package AmqpWorkers
 * @author Alex Panshin <deadyaga@gmail.com>
 * @since 1.0
 */
class Producer
{
    /**
     * @var callable
     */
    private $formatter;

    /**
     * @var bool
     */
    private $isExchange;

    /**
     * @var Queue
     */
    private $queue;

    /**
     * @var Exchange
     */
    private $exchange;

    /**
     * @var AbstractConnection
     */
    private $connection;

    /**
     * Simple fluent constructor to avoid weird-looking constructions like
     *
     * ```php
     * $qos = (new Producer())->toExchange();
     * ```
     *
     * @param AbstractConnection $connection
     * @return Producer
     */
    public static function factory(AbstractConnection $connection)
    {
        return new static($connection);
    }

    /**
     * Producer constructor.
     * @param AbstractConnection $connection
     */
    public function __construct(AbstractConnection $connection)
    {
        $this->connection = $connection;

        $this->withFormatter(function ($message) {
            return (string)$message;
        });
    }

    /**
     * @param Exchange $exchange
     * @return Producer $this
     */
    public function withExchange(Exchange $exchange)
    {
        $this->exchange = $exchange;
        $this->isExchange = true;
        return $this;
    }

    /**
     * @param Queue $queue
     * @return Producer $this
     */
    public function withQueue(Queue $queue)
    {
        $this->queue = $queue;
        $this->isExchange = false;
        return $this;
    }

    /**
     * @return bool
     */
    private function isExchange()
    {
        return $this->isExchange;
    }

    /**
     * @param \Closure|callable $formatter
     * @return Producer $this
     * @throws \AmqpWorkers\Exception\ProducerNotProperlyConfigured
     */
    public function withFormatter($formatter)
    {
        if (!is_callable($formatter)) {
            throw new ProducerNotProperlyConfigured('Formatter must be a callable.');
        }
        $this->formatter = $formatter;

        return $this;
    }

    /**
     * @param array|\Traversable $messages
     * @todo: maybe add properties array as second parameter
     */
    public function produceAll($messages)
    {
        foreach ($messages as $message) {
            $this->produce($message);
        }
    }

    /**
     * @param mixed $payload
     * @todo: maybe add properties array as second parameter
     * @throws \AmqpWorkers\Exception\ProducerNotProperlyConfigured if queue nor exchange not given.
     */
    public function produce($payload)
    {
        $message = new AMQPMessage(call_user_func($this->formatter, $payload));
        $channel = $this->getChannel();

        if ($this->isExchange()) {
            $channel->basic_publish($message, $this->exchange->getName());
        } else {
            $channel->basic_publish($message, '', $this->queue->getName());
        }
    }

    /**
     * @return AMQPChannel
     * @todo: declare queue only once?
     * @throws \AmqpWorkers\Exception\ProducerNotProperlyConfigured
     */
    private function getChannel()
    {
        if ($this->exchange === null && $this->queue === null) {
            throw new ProducerNotProperlyConfigured('Nor queue nor exchange given.');
        }

        $channel = $this->connection->channel();

        if ($this->isExchange()) {
            list ($passive, $durable, $autoDelete, $internal, $nowait, $arguments, $ticket) =
                $this->exchange->listParams();

            $channel->exchange_declare(
                $this->exchange->getName(),
                $this->exchange->getType(),
                $passive,
                $durable,
                $autoDelete,
                $internal,
                $nowait,
                $arguments,
                $ticket
            );
        } else {
            list ($passive, $durable, $exclusive, $autoDelete, $nowait, $arguments, $ticket) =
                $this->queue->listParams();

            $channel->queue_declare(
                $this->queue->getName(),
                $passive,
                $durable,
                $exclusive,
                $autoDelete,
                $nowait,
                $arguments,
                $ticket
            );
        }

        return $channel;
    }
}
