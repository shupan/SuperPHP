<?php

/**
 * User: phil.shu
 * Date: 2017/12/29
 * Time: 上午9:42
 */

namespace Super\Tests\Cache;

use PHPUnit\Framework\TestCase;
use Redis;
use Super\Cache\RedisConnection;
use Super\Cache\RedisStore;
use Mockery as m;

class RedisStoreTest extends TestCase
{

    /**
     * 对缓存的CRUD的操作
     */
    public function testCrudRedis2()
    {

//        $server =
//            [
//                'host' => '192.168.99.100',
//                'port' => 6379,
//                'timeout' => 60
//            ];
//
//
//        $redis = new Redis();
//        $redis->connect($server['host'], $server['port'], $server['timeout']);
//        //$this->assertTrue($connection);
//        $val = $redis->set("foo1", "bar", 0);
//        $this->assertTrue($val);
//        var_dump($redis->get("foo1"));
//        die();
//        $this->assertEquals("bar" , );

//        $store = new RedisStore($redis);
//
//        $store->put("a1", "b1", 1);
//        $this->assertEquals("b1", $store->get("a1"));
//        $this->assertFalse($store->get("333"));

//        $store->put("a1", "b2", 1);
//        $this->assertEquals("b2", $store->get("a1"));
//
//        $store->put("a2", 2, 1);
//        $this->assertEquals(3, $store->increment("a2"));
//
//        $store->forget("a2");
//        $this->assertFalse($store->get("a2"));
//
//        $store->flush();
//        $this->assertFalse($store->get("a1"));
//        $this->assertFalse($store->get("a2"));

    }

    public function testGetReturnsNullWhenNotFound()
    {
        $redis = $this->mockRedis();
        $redis->mockRedis()->shouldReceive('connection')->once()->with('default')->andReturn($redis->getRedis());
        $redis->mockRedis()->shouldReceive('get')->once()->with('prefix:foo')->andReturn(null);
        $this->assertNull($redis->get('foo'));
    }


    public function mockRedis()
    {

        return new RedisStore(m::mock('Super\Api\Redis\Factory', 'preifx'));
    }


}