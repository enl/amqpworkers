<?php


namespace AmqpWorkers\Worker;


interface WorkerInterface
{
    /**
     * @param $message
     * @return mixed
     */
    public function __invoke($message);
}
