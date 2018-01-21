<?php

/**
 * User: phil.shu
 * Date: 2017/12/29
 * Time: 上午9:42
 */

namespace Super\Tests\Support\Structure;

use PHPUnit\Framework\TestCase;
use Super\Support\Structure\Stack;


class StackTest extends TestCase
{


    public function setUp()
    {

    }

    public function testPushOrPop()
    {
        $queue = new Stack(100);
        $queue->push(1);
        $queue->push(2);
        $queue->push(3);
        $this->assertEquals(3, $queue->pop());
        $this->assertEquals(2, $queue->pop());
        $this->assertEquals(1, $queue->pop());
        $this->assertEquals(0, $queue->getLength());
        $this->assertEquals(100, $queue->getMaxLength());

    }

}