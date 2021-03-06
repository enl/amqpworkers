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
 * It can format given messages somehow and can work with both queues and exchanges as a target.
 *
 * @package AmqpWorkers
 * @author Alex Panshin <deadyaga@gmail.com>
 * @since 1.0
 */
class Producer implements ProducerInterface
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
    protected $queue;

    /**
     * @var Exchange
     */
    protected $exchange;

    /**
     * @var AbstractConnection
     */
    private $connection;
    /**
     * @var AMQPChannel
     */
    private $channel;

    /**
     * Simple fluent constructor to avoid weird-looking constructions like
     *
     * ```php
     * $qos = (new Producer())->toExchange();
     * ```
     *
     * @param AbstractConnection $connection
     * @return Producer $this
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
        $this->channel = null;
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
        $this->channel = null;
        return $this;
    }

    /**
     * @param \Closure|callable $formatter
     * @return Producer $this
     * @throws ProducerNotProperlyConfigured
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
     * @param mixed $payload
     * @todo: maybe add properties array as second parameter
     * @throws ProducerNotProperlyConfigured if queue nor exchange not given.
     */
    public function produce($payload)
    {
        if ($this->isExchange()) {
            $this->getChannel()->basic_publish(
                $this->createMessage($payload),
                $this->exchange->getName()
            );
        } else {
            $this->getChannel()->basic_publish(
                $this->createMessage($payload),
                '',
                $this->queue->getName()
            );
        }
    }

    /**
     * Returns `true` if producer is properly configured. Throws exception otherwise.
     * Function is public just because Consumer needs to check if given producer configured before consuming the queue.
     *
     * @return bool
     * @throws ProducerNotProperlyConfigured
     */
    public function selfCheck()
    {
        if ($this->exchange === null && $this->queue === null) {
            throw new ProducerNotProperlyConfigured('Nor queue nor exchange given.');
        }

        return true;
    }

    /**
     * @return AMQPChannel
     * @throws ProducerNotProperlyConfigured
     */
    protected function getChannel()
    {
        $this->selfCheck();

        if (!$this->channel) {
            $this->channel = $this->connection->channel();

            if ($this->isExchange()) {
                list ($passive, $durable, $autoDelete, $internal, $nowait, $arguments, $ticket) =
                    $this->exchange->listParams();

                $this->channel->exchange_declare(
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

                $this->channel->queue_declare(
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
        }

        return $this->channel;
    }

    /**
     * @param mixed $payload
     * @return AMQPMessage
     */
    protected function createMessage($payload)
    {
        return new AMQPMessage(call_user_func($this->formatter, $payload));
    }

    /**
     * @return bool
     */
    protected function isExchange()
    {
        return $this->isExchange;
    }
}
