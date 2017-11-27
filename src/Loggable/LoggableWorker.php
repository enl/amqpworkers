<?php

namespace AmqpWorkers\Loggable;

use AmqpWorkers\Worker\WorkerInterface;
use Psr\Log\LoggerInterface;

class LoggableWorker implements WorkerInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var WorkerInterface
     */
    private $worker;

    /**
     * LoggableWorker constructor.
     *
     * @param LoggerInterface $logger
     * @param WorkerInterface $worker
     */
    public function __construct(LoggerInterface $logger, WorkerInterface $worker)
    {
        $this->logger = $logger;
        $this->worker = $worker;
    }

    /**
     * @param $message
     *
     * @return mixed
     */
    public function __invoke($message)
    {
        $this->logger->debug('Got "%s" message');
        $result = call_user_func($this->worker, $message);
        $this->logger->debug(sprintf('Successfully handled "%s" message', $message));

        return $result;
    }
}
