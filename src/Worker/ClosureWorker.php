<?php


namespace AmqpWorkers\Worker;


class ClosureWorker implements WorkerInterface
{
    /**
     * @var \Closure
     */
    private $closure;

    public function __construct(\Closure $closure)
    {
        $this->closure = $closure;
    }

    /**
     * @param $message
     * @return mixed
     */
    public function invoke($message)
    {
        $function = $this->closure;

        return $function($message);
    }
}
