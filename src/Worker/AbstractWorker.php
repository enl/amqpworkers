<?php


namespace AmqpWorkers\Worker;

abstract class AbstractWorker implements WorkerInterface
{
    /**
     * @param \Closure $closure
     * @return WorkerInterface
     */
    public static function fromClosure(\Closure $closure)
    {
        return new ClosureWorker($closure);
    }
}
