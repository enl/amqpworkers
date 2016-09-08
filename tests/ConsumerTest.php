<?php


namespace AmqpWorkers\Tests;

use AmqpWorkers\Consumer;
use AmqpWorkers\Definition\Queue;
use AmqpWorkers\Exception\ConsumerNotProperlyConfigured;
use AmqpWorkers\Exception\ProducerNotProperlyConfigured;
use AmqpWorkers\Producer;
use AmqpWorkers\Worker\ClosureWorker;
use Mockery as m;
use Mockery\MockInterface;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPLazyConnection;

class ConsumerTest extends MockeryTestCase
{
    /** @var  AMQPLazyConnection|MockInterface */
    private $connection;
    /** @var  AMQPChannel|MockInterface */
    private $channel;

    protected function setUp()
    {
        parent::setUp();
        $this->channel = m::mock(AMQPChannel::class);
        $this->connection = m::mock(AMQPLazyConnection::class);
        $this->connection->shouldReceive('channel')->andReturn($this->channel);
    }

    protected function tearDown()
    {
        parent::tearDown();
        $this->closeMockery();
    }

    public function testEmptyQueue()
    {
        $this->expectException(ConsumerNotProperlyConfigured::class);
        $this->expectExceptionMessage('Queue is not defined.');

        Consumer::factory($this->connection)->run();
    }

    public function testEmptyWorker()
    {
        $this->expectException(ConsumerNotProperlyConfigured::class);
        $this->expectExceptionMessage('Worker is not defined.');

        Consumer::factory($this->connection)->withQueue(new Queue('test'))->run();
    }

    public function testNonConfiguredProducer()
    {
        $this->expectException(ProducerNotProperlyConfigured::class);

        Consumer::factory($this->connection)
            ->withQueue(new Queue('test'))
            ->withWorker(new ClosureWorker(function($message) {}))
            ->produceResult(Producer::factory($this->connection))
            ->run();
    }

}
