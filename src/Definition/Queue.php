<?php


namespace AmqpWorkers\Definition;


class Queue
{
    private $name;

    private $passive = false;

    private $durable = false;

    private $exclusive = false;

    private $autoDelete = false;

    private $nowait = false;

    /**
     * @var null|array
     */
    private $arguments = null;

    private $ticket = null;

    /**
     * @param $name
     * @return static
     */
    public static function factory($name)
    {
        return new static($name);
    }

    /**
     * Queue constructor.
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * @param string $name
     * @return Queue
     */
    public function name($name)
    {
        $this->name = $name;
        return $this;
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
     * This field is necessary without all others so that we have dedicated getter for it.
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
