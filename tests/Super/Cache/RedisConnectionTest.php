<?php

/**
 * User: phil.shu
 * Date: 2017/12/29
 * Time: 上午9:42
 */

namespace Super\Tests\Cache;

use PHPUnit\Framework\TestCase;
use Super\Cache\RedisConnection;

class RedisConnectionTest extends TestCase
{

    private $redis = null;

    /**
     * 测试Memcached的使用
     */
    public function testCRUDConnection()
    {

        $server =
            [
                'host' => '192.168.99.100',
                'port' => 6379,
                'timeout' => 60
            ];
        $this->redis = RedisConnection::getNewInstance($server['host'], $server['port'], $server['timeout']);

        $this->assertNotNull($this->redis);
    }


}