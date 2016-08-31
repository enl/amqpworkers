<?php


namespace AmqpWorkers\Definition;

/**
 * Class Qos
 * @package AmqpWorkers\Definition
 * @author Alex Panshin <deadyaga@gmail.com>
 */
class Qos
{
    private $prefetchSize = 0;

    private $prefetchCount = 0;

    private $global = false;

    /**
     * Simple fluent constructor to avoid weird-looking constructions like
     *
     * ```php
     * $qos = (new Qos())->count(10);
     * ```
     *
     * @return Qos
     */
    public static function factory()
    {
        return new Qos();
    }

    /**
     * Sets `prefetch_size` qos parameter for channel or consumer
     *
     * CAUTION: Looks like phpamqplib did not implement this feature!
     *
     * @see https://www.rabbitmq.com/amqp-0-9-1-reference.html#basic.qos.prefetch-size
     * @default 0 disables limit
     * @param int $value prefetch size in bytes
     * @return $this
     */
    public function size($value)
    {
        $this->prefetchSize = $value;
        return $this;
    }

    /**
     * Sets `prefetch_count` qos parameter for channel or consumer
     *
     * @see https://www.rabbitmq.com/amqp-0-9-1-reference.html#basic.qos.prefetch-count
     * @default 0 disables limit
     * @param int $value prefetch size in items
     * @return $this
     */
    public function count($value)
    {
        $this->prefetchCount = $value;
        return $this;
    }

    /**
     * Sets `global` qos parameter.
     * This parameter defines if those settings should be used for all channel or only for current Consumer.
     * By default settings are consumer-wide.
     *
     * @see https://www.rabbitmq.com/amqp-0-9-1-reference.html#basic.qos.global
     * @default false
     * @param boolean $value
     * @return $this
     */
    public function isGlobal($value)
    {
        $this->global = $value;
        return $this;
    }

    /**
     * Returns all parameters in same order as they are used in PhpAmqpLib\Channel\AMQPChannel::basic_qos()
     *
     * @see \PhpAmqpLib\Channel\AMQPChannel::basic_qos()
     * @return array
     */
    public function listParams()
    {
        return [$this->prefetchSize, $this->prefetchCount, $this->global];
    }
}
