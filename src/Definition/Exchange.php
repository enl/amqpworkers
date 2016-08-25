<?php


namespace AmqpWorkers\Definition;


use AmqpWorkers\Exception\ConfigurationException;

class Exchange
{
    private $name;

    private $type;

    private $passive = false;

    private $durable = false;

    private $internal = false;

    private $autoDelete = false;

    private $nowait = false;

    /**
     * @var null|array
     */
    private $arguments = null;

    private $ticket = null;


    public static function factory($name, $type)
    {
        return new static($name, $type);
    }

    /**
     * Exchange constructor.
     * @param string $name
     * @param string $type
     * @throws \AmqpWorkers\Exception\ConfigurationException
     */
    public function __construct($name, $type)
    {
        if (empty($name)) {
            throw new ConfigurationException('Exchange name cannot be empty.');
        }
        if (empty($type)) {
            throw new ConfigurationException('Exchange type cannot be empty.');
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
     * Returns the list of parameters for queue_declare
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
