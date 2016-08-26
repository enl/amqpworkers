<?php


namespace AmqpWorkers\Tests\Worker;


use AmqpWorkers\Worker\ClosureWorker;

class ClosureWorkerTest extends \PHPUnit_Framework_TestCase
{
    public function testInvoke()
    {
        $called = false;
        $worker = new ClosureWorker(function($value) use (&$called){
            $called = true;
            return $value;
        });

        $actual = $worker('test');
        $this->assertEquals('test', $actual, 'Worker returns given value as Closure does.');
        $this->assertTrue($called, 'Worker actually calls closure.');
    }
}
