<?php


namespace AmqpWorkers\Worker;


interface WorkerInterface
{
    /**
     * @param $message
     * @return mixed
     */
    public function invoke($message);
}
