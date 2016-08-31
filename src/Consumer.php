<?php


namespace AmqpWorkers;

use AmqpWorkers\Definition\Qos;
use AmqpWorkers\Definition\Queue;
use AmqpWorkers\Exception\ConsumerNotProperlyConfigured;
use AmqpWorkers\Worker\WorkerInterface;
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
     * @var Queue
     */
    private $queue;

    /**
     * @var WorkerInterface
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

    /**
     * @param Qos $qos
     * @return Consumer $this
     */
    public function withQos(Qos $qos)
    {
        $this->qos = $qos;
        return $this;
    }

    /**
     * @param Queue $queue
     * @return Consumer $this
     */
    public function withQueue(Queue $queue)
    {
        $this->queue = $queue;
        return $this;
    }

    /**
     * @param WorkerInterface $worker
     * @return Consumer $this
     * @throws \AmqpWorkers\Exception\ConsumerNotProperlyConfigured
     */
    public function withWorker(WorkerInterface $worker)
    {
        $this->worker = $worker;

        return $this;
    }

    /**
     * If producer is set, Consumer will call `Producer::produce` with whatever Worker will return
     *
     * @param Producer $producer
     * @return Consumer $this
     */
    public function produceResult(Producer $producer)
    {
        $this->producer = $producer;
        return $this;
    }

    /**
     * Starts consumer. By default, this function can be terminated only by Worker's exception
     *
     * @throws ConsumerNotProperlyConfigured
     */
    public function run()
    {
        if ($this->queue === null) {
            throw new ConsumerNotProperlyConfigured('Queue is not defined.');
        }
        if ($this->worker === null) {
            throw new ConsumerNotProperlyConfigured('Worker is not defined.');
        }

        $wrapper = function (AMQPMessage $message) {
            $result = call_user_func($this->worker, $message->getBody());

            if ($result && $this->producer) {
                $this->producer->produce($result);
            }

            $message->delivery_info['channel']->basic_ack($message->delivery_info['delivery_tag']);
        };

        $channel = $this->connection->channel();

        // declare queue here
        list ($passive, $durable, $exclusive, $autoDelete, $nowait, $arguments, $ticket) = $this->queue->listParams();
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

        if ($this->qos) {
            list ($size, $count, $global) = $this->qos->listParams();
            $channel->basic_qos($size, $count, $global);
        }

        $channel->basic_consume($this->queue->getName(), '', false, false, false, false, $wrapper, null, []);

        while (count($channel->callbacks)) {
            $channel->wait();
        }

        $channel->close();
    }
}
