<?php


namespace AmqpWorkers\Tests\Worker;


use AmqpWorkers\Worker\AbstractWorker;
use AmqpWorkers\Worker\ClosureWorker;

class AbstractWorkerTest extends \PHPUnit_Framework_TestCase
{
    public function testFromClosure()
    {
        $closure = function($value) { return $value; };
        $actual = AbstractWorker::fromClosure($closure);

        $this->assertInstanceOf(ClosureWorker::class, $actual);
    }
}
