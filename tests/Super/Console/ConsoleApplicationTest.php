<?php

namespace Super\Tests\Console;

use Mockery as m;
use PHPUnit\Framework\TestCase;

class ConsoleApplicationTest extends TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testAddSetsAppInstance()
    {
        $app = $this->getMockConsole(['addToParent']);
        $command = m::mock('Super\Console\Command');
        $command->shouldReceive('setApp')->once()->with(m::type('Super\Api\Foundation\Application'));
        $app->expects($this->once())->method('addToParent')->with($this->equalTo($command))->will($this->returnValue($command));
        $result = $app->add($command);

        $this->assertEquals($command, $result);
    }


    protected function getMockConsole(array $methods)
    {
        $app = m::mock('Super\Api\Foundation\Application', ['version' => '1.0.0']);
        $events = m::mock('Super\Api\Events\Dispatcher', ['dispatch' => null]);

        $console = $this->getMockBuilder('Super\Console\Application')->setMethods($methods)->setConstructorArgs([
            $app, $events, 'test-version',
        ])->getMock();

        return $console;
    }
}
