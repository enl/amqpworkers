<?php


namespace AmqpWorkers\Tests;


use AmqpWorkers\BatchProducer;
use AmqpWorkers\Definition\Exchange;
use AmqpWorkers\Definition\Queue;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPLazyConnection;
use PhpAmqpLib\Message\AMQPMessage;

class BatchProducerTest extends MockeryTestCase
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

    public function testProduceToQueue()
    {
        $this->channel->shouldReceive('queue_declare');
        $this->channel->shouldReceive('batch_basic_publish')->once()->withArgs([m::anyOf(new AMQPMessage('test')), '', 'test']);
        $this->channel->shouldReceive('batch_basic_publish')->once()->withArgs([m::anyOf(new AMQPMessage('test1')), '', 'test']);
        $this->channel->shouldReceive('batch_basic_publish')->once()->withArgs([m::anyOf(new AMQPMessage('test2')), '', 'test']);
        $this->channel->shouldReceive('publish_batch')->once();

        BatchProducer::factory($this->connection)
            ->withQueue(new Queue('test'))
            ->produce(['test', 'test1', 'test2']);
    }

    public function testProduceToExchange()
    {
        $this->channel->shouldReceive('exchange_declare');
        $this->channel->shouldReceive('batch_basic_publish')->once()->withArgs([m::anyOf(new AMQPMessage('test')), 'test']);
        $this->channel->shouldReceive('batch_basic_publish')->once()->withArgs([m::anyOf(new AMQPMessage('test1')), 'test']);
        $this->channel->shouldReceive('batch_basic_publish')->once()->withArgs([m::anyOf(new AMQPMessage('test2')), 'test']);
        $this->channel->shouldReceive('publish_batch')->once();

        BatchProducer::factory($this->connection)
            ->withExchange(new Exchange('test', 'fanout'))
            ->produce(['test', 'test1', 'test2']);
    }
}
