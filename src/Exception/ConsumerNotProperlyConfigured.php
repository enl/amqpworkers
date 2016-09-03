<?php


namespace AmqpWorkers\Exception;

/**
 * This exception is thrown if you try to `run()` Consumer before binding it to queue or attaching worker.
 *
 * @package AmqpWorkers\Exception
 * @author Alex Panshin <deadyaga@gmail.com>
 * @since 1.0
 */
class ConsumerNotProperlyConfigured extends \LogicException
{

}
