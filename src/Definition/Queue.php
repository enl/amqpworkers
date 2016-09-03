<?php


namespace AmqpWorkers\Definition;

use AmqpWorkers\Exception\DefinitionException;

/**
 * Queue definition.
 * Use it with `AmqpWorkers\Producer::withQueue` or `AmqpWorkers\Consumer::withQueue`.
 *
 * @package AmqpWorkers\Definition
 * @author Alex Panshin <deadyaga@gmail.com>
 * @since 1.0
 */
class Queue
{
    /** @var string  */
    private $name;

    /** @var bool  */
    private $passive = false;

    /** @var bool  */
    private $durable = false;

    /** @var bool  */
    private $exclusive = false;

    /** @var bool  */
    private $autoDelete = false;

    /** @var bool  */
    private $nowait = false;

    /** @var array|null */
    private $arguments = null;

    /** @var int|null  */
    private $ticket = null;

    /**
     * @param string $name
     * @return static
     * @throws \AmqpWorkers\Exception\DefinitionException
     */
    public static function factory($name)
    {
        return new static($name);
    }

    /**
     * Queue constructor.
     * @param string $name
     * @throws \AmqpWorkers\Exception\DefinitionException if empty name is given
     */
    public function __construct($name)
    {
        if ($name === null || $name === '') {
            throw new DefinitionException('Queue name cannot be empty.');
        }
        $this->name = (string) $name;
    }

    /**
     * @see https://www.rabbitmq.com/amqp-0-9-1-reference.html#queue.declare.passive
     * @default false
     * @param $passive
     * @return Queue
     */
    public function passive($passive)
    {
        $this->passive = $passive;
        return $this;
    }

    /**
     * @see https://www.rabbitmq.com/amqp-0-9-1-reference.html#queue.declare.durable
     * @default false
     * @param bool $durable
     * @return $this
     */
    public function durable($durable)
    {
        $this->durable = $durable;
        return $this;
    }

    /**
     * @see https://www.rabbitmq.com/amqp-0-9-1-reference.html#queue.declare.exclusive
     * @default false
     * @param bool $exclusive
     * @return $this
     */
    public function exclusive($exclusive)
    {
        $this->exclusive = $exclusive;
        return $this;
    }

    /**
     * @see https://www.rabbitmq.com/amqp-0-9-1-reference.html#queue.declare.auto-delete
     * @default false
     * @param bool $autoDelete
     * @return $this
     */
    public function autoDelete($autoDelete)
    {
        $this->autoDelete = $autoDelete;
        return $this;
    }

    /**
     * @see https://www.rabbitmq.com/amqp-0-9-1-reference.html#queue.declare.no-wait
     * @default false
     * @param bool $nowait
     * @return $this
     */
    public function nowait($nowait)
    {
        $this->nowait = $nowait;
        return $this;
    }

    /**
     * @see https://www.rabbitmq.com/amqp-0-9-1-reference.html#queue.declare.arguments
     * @default null
     * @param array $arguments
     * @return $this
     */
    public function arguments(array $arguments)
    {
        $this->arguments = $arguments;
        return $this;
    }

    /**
     * @default null
     * @param int|null $ticket
     * @return $this
     */
    public function ticket($ticket)
    {
        $this->ticket = $ticket;
        return $this;
    }

    /**
     * This field is necessary without all others so that we have dedicated getter for it.
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the list of parameters for queue_declare
     *
     * @see \Phpamqplib\Channel\AMQPChannel::queue_declare()
     * @return array
     */
    public function listParams()
    {
        return [
            $this->passive,
            $this->durable,
            $this->exclusive,
            $this->autoDelete,
            $this->nowait,
            $this->arguments,
            $this->ticket
        ];
    }
}
