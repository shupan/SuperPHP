<?php

/**
 * User: phil.shu
 * Date: 2017/12/29
 * Time: 上午9:42
 */

namespace Super\Tests\Cache;

use PHPUnit\Framework\TestCase;
use Super\Support\Sort;


class SortTest extends TestCase
{

    /**
     * 排序的支持
     * @var \Super\Support\Sort
     */
    private $sort = null;

    public function setUp()
    {

        $this->sort = new Sort();
    }

    public function testBubbleSort()
    {
        $arr = [3, 8, 2, 3, 4, 10];
        $data = $this->sort->bubbleSort($arr);

        $this->assertEquals([
            2, 3, 3, 4, 8, 10
        ], $data);

        $arr = [3, 1, 2, 3, 4, 10];
        $data = $this->sort->bubbleSort($arr);

        $this->assertEquals([
            1,2, 3, 3, 4, 10
        ], $data);


    }

    public function testInsertSort()
    {
        $arr = [3, 8, 2, 3, 4, 10];
        $data = $this->sort->insertSort($arr);

        $this->assertEquals([
            2, 3, 3, 4, 8, 10
        ], $data);

    }

}