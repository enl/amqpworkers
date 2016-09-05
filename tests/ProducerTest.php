<?php


namespace AmqpWorkers\Tests;

use AmqpWorkers\Definition\Exchange;
use AmqpWorkers\Definition\Queue;
use Mockery as m;
use AmqpWorkers\Exception\ProducerNotProperlyConfigured;
use AmqpWorkers\Producer;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPLazyConnection;
use PhpAmqpLib\Message\AMQPMessage;

class ProducerTest extends m\Adapter\Phpunit\MockeryTestCase
{
    /**
     * @var AMQPChannel|m\MockInterface
     */
    private $channel;
    /**
     * @var AMQPLazyConnection|m\MockInterface
     */
    private $connection;

    protected function setUp()
    {
        $this->channel = m::mock(AMQPChannel::class);
        $this->connection = m::mock(AMQPLazyConnection::class);
        $this->connection->shouldReceive('channel')->andReturn($this->channel);
    }

    protected function tearDown()
    {
        $this->closeMockery();
        parent::tearDown();
    }


    /**
     * @test
     */
    public function shouldThrowException()
    {
        $this->expectException(ProducerNotProperlyConfigured::class);
        $this->expectExceptionMessage('Nor queue nor exchange given.');

        $producer = new Producer(new AMQPLazyConnection('test', 'test', 'test', 'test'));
        $producer->produce('test');
    }

    public function testWithFormatter()
    {
        $this->expectException(ProducerNotProperlyConfigured::class);
        $this->expectExceptionMessage('Formatter must be a callable.');

        $producer = new Producer($this->connection);
        $producer->withFormatter('i-am-not-callable');
    }

    /**
     * @test
     */
    public function shouldFormatMessageWithGivenFormatter()
    {
        $called = false;
        $producer = new Producer($this->connection);
        $producer->withQueue(new Queue('test-queue'));
        $producer->withFormatter(function($message) use (&$called){
            $called = true;
            return 'test'.$message;
        });
        $this->channel->shouldReceive('queue_declare');
        $this->channel->shouldReceive('basic_publish')->once()->withArgs([m::anyOf(new AMQPMessage('testtest')), '', 'test-queue']);

        $producer->produce('test');
        $this->assertTrue($called, 'Producer called given formatter');
    }

    public function testProduceToExchange()
    {
        $this->channel->shouldReceive('exchange_declare')->once()->withArgs(['exchange', 'fanout', false, false, false, false, false, [], null]);
        $this->channel->shouldReceive('basic_publish')->once()->withArgs([M::anyOf(new AMQPMessage('message')), 'exchange']);

        Producer::factory($this->connection)
            ->withExchange(new Exchange('exchange', 'fanout'))
            ->produce('message');
    }

    public function testProduceToQueue()
    {
        $this->channel->shouldReceive('queue_declare')->once()->withArgs(['queue', false, false, false, false, false, [], null]);
        $this->channel->shouldReceive('basic_publish')->once()->withArgs([M::anyOf(new AMQPMessage('message')), '', 'queue']);

        Producer::factory($this->connection)
            ->withQueue(new Queue('queue'))
            ->produce('message');
    }


}
