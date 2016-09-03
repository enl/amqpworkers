<?php


namespace AmqpWorkers\Exception;

/**
 * This exception is thrown if you try to `produce()` something with producer which is not properly confgiured.
 * For example, if you call `produce()` before `withExchange()`
 *
 * @package AmqpWorkers\Exception
 * @author Alex Panshin <deadyaga@gmail.com>
 * @since 1.0
 */
class ProducerNotProperlyConfigured extends \LogicException
{

}
