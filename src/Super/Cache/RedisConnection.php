<?php

namespace Super\Cache;

use Redis;

/**
 * 描述:
 *    支持多个Redis的服务器的连接操作.
 * 并提供统一的Memcached的管理机制
 * User: phil.shu
 * Date: 2017/12/29
 * Time: 上午9:30
 */
class RedisConnection
{

    protected static $redis = null;

    private function __construct()
    {

    }

    /**
     * 获取Redis 实例,并且采用单例的模式来调用
     *
     * @param $host
     * @param int $port
     * @param float $timeout
     * @return mixed
     */
    public static function getNewInstance($host, $port = 6379, $timeout = 0.0)
    {

        if (empty($redis)) {
            $redisClient = new Redis();
            static::$redis = $redisClient->connect($host, $port, $timeout);
        }
        return static::$redis;
    }
}