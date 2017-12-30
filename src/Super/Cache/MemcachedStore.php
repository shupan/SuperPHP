<?php

namespace Super\Cache;

use Super\Api\Cache\Store;

/**
 * User: phil.shu
 * Date: 2017/12/29
 * Time: 上午9:30
 */
class MemcachedStore implements Store
{
    use GetMultipleKeys;

    /**
     * 缓存连接的对象
     * @var null
     */
    private $cache = null;


    public function __construct( array $servers, $connectionId = null, array $options = [], array $credentials = [] )
    {

        $mc = new MemcachedConnection();
        $this->cache = $mc->connect($servers, $connectionId, $options, $credentials);
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
     * @param  float|int $minutes memcached set默认是秒
     * @return void
     */
    public function put($key, $value, $minutes)
    {

        return $this->cache->set($key, $value , $minutes * 60 );
    }

    /**
     * 删除一个缓存
     *
     * @param  string $key
     * @return bool
     */
    public function forget($key)
    {
        return $this->cache->delete($key);
    }

    /**
     * 删除所有的缓存
     *
     * @return bool
     */
    public function flush()
    {
        return $this->cache->flush();
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
        return $this->cache->increment($key , $value);
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
        return $this->increment($key , -1 * $value);
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

        return $this->put($key , $value , 0);
    }

    /**
     * 获取缓存的前缀
     *
     * @return string
     */
    public function getPrefix()
    {
        return "";
    }


}