<?php


namespace AmqpWorkers\Exception;

/**
 * This exception is thrown by Definition classes if their parameters are wrong.
 * For example, if you provide empty queue name.
 *
 * @package AmqpWorkers\Exception
 * @author Alex Panshin <deadyaga@gmail.com>
 * @since 1.0
 */
class DefinitionException extends \LogicException
{

}
