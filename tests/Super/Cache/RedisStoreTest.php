<?php

/**
 * User: phil.shu
 * Date: 2017/12/29
 * Time: 上午9:42
 */

namespace Super\Tests\Cache;

use PHPUnit\Framework\TestCase;
use Super\Cache\RedisConnection;

class RedisStoreTest extends TestCase
{

    /**
     * 对缓存的CRUD的操作
     */
    public function testCrudRedis()
    {

        $server =
            [
                'host' => '192.168.99.100',
                'port' => 11211,
                'timeout' => 60
            ];

        $store = RedisConnection::getNewInstance($server['host'] ,  $server['port'] , $server['timeout']);

        $store->put("a1", "b1", 1);
        $this->assertEquals("b1", $store->get("a1"));
        $this->assertFalse($store->get("333"));

        $store->put("a1", "b2", 1);
        $this->assertEquals("b2", $store->get("a1"));

        $store->put("a2", 2, 1);
        $this->assertEquals(3, $store->increment("a2"));

        $store->forget("a2");
        $this->assertFalse($store->get("a2"));

        $store->flush();
        $this->assertFalse($store->get("a1"));
        $this->assertFalse($store->get("a2"));

    }


}