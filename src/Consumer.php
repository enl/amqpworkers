<?php


namespace AmqpWorkers;


use AmqpWorkers\Definition\Qos;
use AmqpWorkers\Exception\ConsumerNotProperlyConfigured;
use PhpAmqpLib\Connection\AbstractConnection;
use PhpAmqpLib\Message\AMQPMessage;

class Consumer
{
    /**
     * @var AbstractConnection
     */
    private $connection;

    /**
     * @var null|Qos
     */
    private $qos;

    /**
     * @var string
     */
    private $queueName;

    /**
     * @var callable
     */
    private $worker;

    /**
     * @var null|Producer
     */
    private $producer;


    /**
     * Simple fluent constructor to avoid weird-looking constructions like
     *
     * ```php
     * $qos = (new Consumer())->run();
     * ```
     *
     * @param AbstractConnection $connection
     * @return Consumer
     */
    public static function factory(AbstractConnection $connection)
    {
        return new static($connection);
    }

    /**
     * Consumer constructor.
     * @param AbstractConnection $connection
     */
    public function __construct(AbstractConnection $connection)
    {
        $this->connection = $connection;
    }

    public function withQos(Qos $qos)
    {
        $this->qos = $qos;
    }

    /**
     * @param string $queueName
     * @param callable $worker
     * @throws \AmqpWorkers\Exception\ConsumerNotProperlyConfigured
     */
    public function listen($queueName, $worker)
    {
        if (!is_callable($this->worker)) {
            throw new ConsumerNotProperlyConfigured('Worker must be callable.');
        }

        $this->queueName = $queueName;
        $this->worker = $worker;
    }

    /**
     * If producer is set, Consumer will call `Producer::produce` with whatever Worker will return
     *
     * @param Producer $producer
     */
    public function produceResult(Producer $producer)
    {
        $this->producer = $producer;
    }

    public function run()
    {
        if ($this->queueName === null) {
            throw new ConsumerNotProperlyConfigured('Queue name is not given.');
        }
        if ($this->worker = null) {
            throw new ConsumerNotProperlyConfigured('Worker is not defined.');
        }

        $channel = $this->connection->channel();

        if ($this->qos) {
            list ($size, $count, $global) = $this->qos->values();
            $channel->basic_qos($size, $count, $global);
        }

        $wrapper = function(AMQPMessage $message) {
            $result = call_user_func($this->worker, $message->getBody());
            $message->delivery_info['channel']->basic_ack($message->delivery_info['delivery_tag']);

            if ($this->producer) {
                $this->producer->produce($result);
            }
        };

        // declare queue here
        $channel->basic_consume($this->queueName, '', false, false, false, false, $wrapper, null, []);

        while(count($channel->callbacks)) {
            $channel->wait();
        }

        $channel->close();
    }
}
