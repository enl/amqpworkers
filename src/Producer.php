<?php


namespace AmqpWorkers;

use AmqpWorkers\Exception\ProducerNotProperlyConfigured;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AbstractConnection;
use PhpAmqpLib\Message\AMQPMessage;

class Producer
{
    /**
     * @var callable
     */
    private $formatter;

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

    public function __construct(AbstractConnection $connection)
    {
        $this->connection = $connection;

        $this->setFormatter(function ($message) {
            return (string) $message;
        });
    }

    public function withExchange()
    {

    }

    public function withQueue()
    {

    }

    private function isExchange()
    {

    }

    public function setFormatter($formatter)
    {
        if (is_callable($formatter)) {
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
     */
    public function produce($payload)
    {
        $message = new AMQPMessage(call_user_func($this->formatter, $payload));

        if ($this->isExchange()) {
            $this->getChannel()->basic_publish($message, $this->name);
        } else {
            $this->getChannel()->basic_publish($message, '', $this->name);
        }
    }

    /**
     * @return AMQPChannel
     */
    private function getChannel()
    {
        $channel = $this->connection->channel();

        if ($this->isExchange()) {
            // declare exchange here
        } else {
            // declare queue here
        }

        return $channel;
    }
}
