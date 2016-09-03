<?php


namespace AmqpWorkers\Definition;

use AmqpWorkers\Exception\DefinitionException;

/**
 * Exchange definition. Use it with `Producer::withExchange()`
 * if you need to send something to RabbitMQ into explicitly defined exchange.
 *
 * @see https://www.rabbitmq.com/tutorials/amqp-concepts.html
 *
 * @package AmqpWorkers\Definition
 * @author Alex Panshin <deadyaga@gmail.com>
 * @since 1.0
 */
class Exchange
{
    /** @var string  */
    private $name;

    /** @var string  */
    private $type;

    /** @var bool  */
    private $passive = false;

    /** @var bool  */
    private $durable = false;

    /** @var bool  */
    private $internal = false;

    /** @var bool  */
    private $autoDelete = false;

    /** @var bool  */
    private $nowait = false;

    /** @var array|null  */
    private $arguments = null;

    /** @var int|null */
    private $ticket = null;


    /**
     * Named constructor to get more fluent interface
     * @param $name
     * @param $type
     * @return static
     */
    public static function factory($name, $type)
    {
        return new static($name, $type);
    }

    /**
     * Exchange constructor.
     * @param string $name Exchange name
     * @param string $type Exchange type.
     *
     * @see https://www.rabbitmq.com/tutorials/amqp-concepts.html#exchanges
     * @throws \AmqpWorkers\Exception\DefinitionException
     */
    public function __construct($name, $type)
    {
        if (empty($name)) {
            throw new DefinitionException('Exchange name cannot be empty.');
        }
        if (empty($type)) {
            throw new DefinitionException('Exchange type cannot be empty.');
        }

        $this->name = $name;
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @see https://www.rabbitmq.com/amqp-0-9-1-reference.html#exchange.declare.passive
     * @default false
     * @param $passive
     * @return Exchange $this
     */
    public function passive($passive)
    {
        $this->passive = $passive;
        return $this;
    }

    /**
     * @see https://www.rabbitmq.com/amqp-0-9-1-reference.html#exchange.declare.durable
     * @default false
     * @param bool $durable
     * @return Exchange $this
     */
    public function durable($durable)
    {
        $this->durable = $durable;
        return $this;
    }

    /**
     * @see https://www.rabbitmq.com/amqp-0-9-1-reference.html#exchange.declare.auto-delete
     * @default false
     * @param bool $autoDelete
     * @return Exchange $this
     */
    public function autoDelete($autoDelete)
    {
        $this->autoDelete = $autoDelete;
        return $this;
    }

    /**
     * @see * @see https://www.rabbitmq.com/amqp-0-9-1-reference.html#exchange.declare.internal
     * @default false
     * @param $internal
     * @return Exchange $this
     */
    public function internal($internal)
    {
        $this->internal = $internal;
        return $this;
    }

    /**
     * @see https://www.rabbitmq.com/amqp-0-9-1-reference.html#exchange.declare.no-wait
     * @default false
     * @param bool $nowait
     * @return Exchange $this
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
     * Returns the list of parameters for exchange_declare
     *
     * @see \PhpAmqpLib\Channel\AMQPChannel::exchange_declare()
     * @return array
     */
    public function listParams()
    {
        return [
            $this->passive,
            $this->durable,
            $this->autoDelete,
            $this->internal,
            $this->nowait,
            $this->arguments,
            $this->ticket
        ];
    }
}
