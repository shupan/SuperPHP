<?php

namespace Super\Cache;

use Redis;
use Super\Api\Cache\Store;
use Super\Cache;


/**
 * User: phil.shu
 * Date: 2017/12/29
 * Time: 上午9:30
 */

class RedisStore implements Store
{

    private $prefix = '';


    /**
     * 缓存连接的对象
     * @var null
     */
    private $cache = null;


    public function __construct(Redis $redis, $prefix = '')
    {

        $this->cache = $redis;
        $this->prefix = $this->prefix;
    }


    /**
     * 根据Key 获取字符或数组
     *
     * @param  string|array $key
     * @return mixed
     */
    public function get($key)
    {
        return $this->cache->get($key);
    }

    /**
     * 存储一个缓存,并制定失效时间
     *
     * @param  string $key
     * @param  mixed $value
     * @param  float|int $minutes redis set默认是秒
     * @return void
     */
    public function put($key, $value, $minutes)
    {

        $return = $this->cache->set($key, $value , $minutes * 60);
        return $return;
    }

    /**
     * 删除一个缓存
     *
     * @param  string $key
     * @return bool
     */
    public function forget($key)
    {
        return $this->cache->del($key);
    }

    /**
     * 删除所有的缓存
     *
     * @return bool
     */
    public function flush()
    {
        return $this->cache->flushDB();
    }


    /**
     * 对缓存的key数值增加
     *
     * @param  string $key
     * @param  mixed $value
     * @return int|bool
     */
    public function increment($key, $value = 1)
    {
        return $this->cache->incrBy($key, $value);
    }

    /**
     * 对缓存的key数值减少
     *
     * @param  string $key
     * @param  mixed $value
     * @return int|bool
     */
    public function decrement($key, $value = 1)
    {
        return $this->cache->decrBy($key , $value);
    }

    /**
     * 设置这个缓存,不设置失效时间
     *
     * @param  string $key
     * @param  mixed $value
     * @return void
     */
    public function forever($key, $value)
    {

        return $this->cache->set($key , $value ,0);
    }

    /**
     * 获取缓存的前缀
     *
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    public function setPrefix($prefix){

        $this->prefix = $prefix;
    }


    /**
     * 可以获取多个key,如果返回的key里面是没有找到怎么会返回一个null值
     *
     * @param  array $keys
     * @return array
     */
    public function many(array $keys)
    {
        return $this->cache->getMultiple($keys);
    }

    /**
     *  一次性存放很多的缓存的数组,并指定失效时间
     *  采用Redis的事务处理机制,保证数据批量请求的原子性
     * @param  array $values
     * @param  float|int $minutes
     * @return void
     */
    public function putMany(array $values, $minutes)
    {
        $this->cache->multi();

        foreach ($values as $key => $value) {
            $this->put($key, $value, $minutes);
        }

        return $this->cache->exec();
    }
}