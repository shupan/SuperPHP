<?php

/**
 * User: phil.shu
 * Date: 2017/12/29
 * Time: 上午9:42
 */

namespace Super\Tests\Cache;

use PHPUnit\Framework\TestCase;
use Super\Cache\ArrayStore;

class ArrayStoreTest extends TestCase
{


    public function testGet()
    {

        $store = new ArrayStore();
        $store->put("aaa", "aaa", 1);
        $this->assertEquals('aaa', $store->get("aaa"));
        $store->put("b1", "bb1", 1);
        $store->put('b2', "bb2", 1);
        $this->assertEquals("bb2", $store->get("b2"));
    }


    /**
     * 检查设置的缓存是否可以被查询到
     */
    public function testPutToGet()
    {

        $store = new ArrayStore();
        $store->put("k1", "v1", 10);
        $this->assertEquals('v1', $store->get("k1"));

    }

    /**
     * 设置多个缓存是否可以被查到
     */
    public function testPutManyToGetMany()
    {
        $store = new ArrayStore();
        $expect = [
            "foo" => 'fv1',
            "goo" => 'gv1',
            'test' => 't1'
        ];
        $store->putMany($expect, 10);
        $this->assertEquals([
            'fv1',
            'gv1',
            't1',
        ], $store->many([
            'foo',
            'goo',
            'test'
        ]));

    }

    /**
     * 测试缓存数值计算，增加和减少
     */
    public function testIncrementOrDecrement()
    {

        $store = new ArrayStore();
        $store->put("test", 1, 10);
        $store->increment('test', 1);
        $this->assertEquals(2, $store->get('test'));
        $store->put("t2", 20, 1);
        $store->decrement("t2");
        $this->assertEquals(19, $store->get("t2"));
    }

    /**
     * 测试缓存的删除是否有影响
     */
    public function testItemCanRemoved()
    {

        $store = new ArrayStore();
        $store->put("test1", "t1", 1);
        $store->put("test2", "t2", 1);
        $store->forget('test1');
        $this->assertNull($store->get('test1'));
        $this->assertNotNull($store->get("test2"));
    }

    /**
     *  清理所有的缓存信息
     */
    public function testFlush()
    {

        $store = new ArrayStore;
        $store->put('foo', 'bar', 10);
        $store->put('baz', 'boom', 10);
        $result = $store->flush();
        $this->assertTrue($result);
        $this->assertNull($store->get('foo'));
        $this->assertNull($store->get('baz'));
    }

}