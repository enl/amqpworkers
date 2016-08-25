<?php


namespace AmqpWorkers\Tests\Definition;


use AmqpWorkers\Definition\Exchange;
use AmqpWorkers\Exception\ConfigurationException;

class ExchangeTest extends \PHPUnit_Framework_TestCase
{
    public function exceptionProvider()
    {
        return [
            'empty name' => ['', '', 'Exchange name cannot be empty.'],
            'empty type' => ['queue', '', 'Exchange type cannot be empty.']
        ];
    }

    /**
     * @param string $name
     * @param string $type
     * @param string $message
     * @test
     * @dataProvider exceptionProvider
     */
    public function shouldRaiseException($name, $type, $message)
    {
        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage($message);

        Exchange::factory($name, $type);
    }

    /**
     * @test
     */
    public function shouldReturnRequiredValues()
    {
        $exchange = Exchange::factory('name', 'type');

        $this->assertEquals('name', $exchange->getName());
        $this->assertEquals('type', $exchange->getType());
    }

    /**
     * @test
     */
    public function shouldReturnDefaultValues()
    {
        $expected = [false, false, false, false, false, null, null];
        $actual = Exchange::factory('queue', 'fanout')->listParams();

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function shouldReturnGivenValues()
    {
        $expected = [true, true, true, true, true, ['test' => 'test'], 1];
        $queue = Exchange::factory('queue', 'fanout')
            ->passive(true)
            ->durable(true)
            ->autoDelete(true)
            ->internal(true)
            ->nowait(true)
            ->arguments(['test' => 'test'])
            ->ticket(1);

        $this->assertEquals($expected, $queue->listParams());
    }
}
