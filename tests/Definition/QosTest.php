<?php


namespace AmqpWorkers\Tests\Definition;


use AmqpWorkers\Definition\Qos;

class QosTest extends \PHPUnit_Framework_TestCase
{
    public function testValues()
    {
        $expected = [10, 10, true];
        $actual = Qos::factory()->size(10)->count(10)->isGlobal(true)->listParams();

        $this->assertEquals($expected, $actual);
    }

    public function testDefaultValues()
    {
        $expected = [0, 0, false];
        $actual = Qos::factory()->listParams();

        $this->assertEquals($expected, $actual);
    }
}
