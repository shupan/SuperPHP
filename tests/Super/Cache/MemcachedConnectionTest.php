<?php

/**
 * User: phil.shu
 * Date: 2017/12/29
 * Time: 上午9:42
 */

namespace Super\Tests\Cache;

use PHPUnit\Framework\TestCase;
use Super\Cache\MemcachedConnection;

class MemcachedConnectionTest extends TestCase
{

    private $mc = null ;

    /**
     * 测试Memcached的使用
     */
    public function testCRUDConnection(){


        $this->mc  = new MemcachedConnection();
        $servers = [
            [
                'host' => '192.168.99.100',
                'port' => 11211,
                'weight' => 1000
            ]
        ];
        $memcached = $this->mc->connect($servers);
        $this->assertNotNull($memcached);
    }


}