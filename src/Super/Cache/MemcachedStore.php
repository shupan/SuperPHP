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

    private $prefix = '';

    /**
     * 缓存连接的对象
     * @var null
     */
    private $cache = null;


    public function __construct(\Memcached $memcached, $prefix = '')
    {
        $this->cache = $memcached;
        $this->prefix = $prefix;
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

        return $this->cache->set($key, $value, $minutes * 60);
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
        return $this->cache->increment($key, $value);
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
        return $this->cache->decrement($key, $value);
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

        return $this->put($key, $value, 0);
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

    /**
     * 设置前缀
     * @param $prefix
     */
    public function setPrefix($prefix)
    {

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

        return $this->cache->getMulti($keys);
    }

    /**
     *  一次性存放很多的缓存的数组,并指定失效时间
     *
     * @param  array $values
     * @param  float|int $minutes
     * @return void
     */
    public function putMany(array $values, $minutes)
    {
        return $this->cache->setMulti($values, $minutes * 60);
    }
}