<?php

namespace AmqpWorkers\Loggable;

use AmqpWorkers\Exception\ProducerNotProperlyConfigured;
use AmqpWorkers\ProducerInterface;
use Psr\Log\LoggerInterface;

/**
 * This class is loggable implementation of Producer. It's just a decorator,
 * which delegates real producing into given implementation, and just logs all the events
 * @package AmqpWorkers\Loggable
 */
class LoggableProducer implements ProducerInterface
{

    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var ProducerInterface
     */
    private $producer;

    public function __construct(LoggerInterface $logger, ProducerInterface $producer)
    {
        $this->logger = $logger;
        $this->producer = $producer;
    }

    /**
     * @param mixed $payload
     *
     * @todo: maybe add properties array as second parameter
     * @throws ProducerNotProperlyConfigured if queue nor exchange not given.
     */
    public function produce($payload)
    {
        $this->logger->debug('Got "%s" message, producing...', serialize($payload));
        $this->producer->produce($payload);
        $this->logger->debug('Produced "%s" message', serialize($payload));
    }

    public function selfCheck()
    {
        return $this->producer->selfCheck();
    }
}
