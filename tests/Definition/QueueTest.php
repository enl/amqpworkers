<?php


namespace AmqpWorkers\Tests\Definition;


use AmqpWorkers\Definition\Queue;
use AmqpWorkers\Exception\DefinitionException;

class QueueTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldRaiseExceptionIfNameIsNull()
    {
        $this->expectException(DefinitionException::class);
        $this->expectExceptionMessage('Queue name cannot be empty.');

        Queue::factory('');
    }

    /**
     * @test
     */
    public function shouldReturnDefaultValues()
    {
        $expected = [false, false, false, false, false, null, null];
        $actual = Queue::factory('queue')->listParams();

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function shouldReturnGivenValues()
    {
        $expected = [true, true, true, true, true, ['test' => 'test'], 1];
        $queue = Queue::factory('queue')
            ->passive(true)
            ->durable(true)
            ->autoDelete(true)
            ->exclusive(true)
            ->nowait(true)
            ->arguments(['test' => 'test'])
            ->ticket(1);

        $this->assertEquals($expected, $queue->listParams());
        $this->assertEquals('queue', $queue->getName());
    }
}
