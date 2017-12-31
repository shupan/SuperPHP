<?php

namespace Super\Api\Cache;

use Closure;

/**
 * 获取缓存的数据仓库
 *
 * Interface Repository
 * @package Super\Api\Cache
 */
interface Repository
{
    /**
     * 判断缓存项是否存在
     *
     * @param  string  $key
     * @return bool
     */
    public function has($key);

    /**
     * 根据缓存的key获取对象
     *
     * @param  string  $key
     * @param  mixed   $default
     * @return mixed
     */
    public function get($key, $default = null);

    /**
     * 通过key获取缓存对象并且删除该缓存对象
     *
     * @param  string  $key
     * @param  mixed   $default
     * @return mixed
     */
    public function pull($key, $default = null);

    /**
     * 存储对象缓存中
     *
     * @param  string  $key
     * @param  mixed   $value
     * @param  \DateTime|float|int  $minutes
     * @return void
     */
    public function put($key, $value, $minutes);

    /**
     * 存储对象缓存中,如果该缓存不在情况
     *
     * @param  string  $key
     * @param  mixed   $value
     * @param  \DateTime|float|int  $minutes
     * @return bool
     */
    public function add($key, $value, $minutes);

    /**
     * 缓存值增加器
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return int|bool
     */
    public function increment($key, $value = 1);

    /**
     * 缓存值减少器
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return int|bool
     */
    public function decrement($key, $value = 1);

    /**
     * 存储的缓存值一直不失效
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return void
     */
    public function forever($key, $value);

    /**
     * 存储key缓存后进行回调
     *
     * @param  string  $key
     * @param  \DateTime|float|int  $minutes
     * @param  \Closure  $callback
     * @return mixed
     */
    public function remember($key, $minutes, Closure $callback);

    /**
     * 查询到缓存后触发回调
     *
     * @param  string   $key
     * @param  \Closure  $callback
     * @return mixed
     */
    public function sear($key, Closure $callback);

    /**
     * 获得一个不失效的缓存,触发回调
     *
     * @param  string   $key
     * @param  \Closure  $callback
     * @return mixed
     */
    public function rememberForever($key, Closure $callback);

    /**
     * 删除一个缓存
     *
     * @param  string $key
     * @return bool
     */
    public function forget($key);
}
