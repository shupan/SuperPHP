<?php

namespace Super\Api\Cache;

/**
 * Created by PhpStorm.
 * User:  Phil.shu
 * Date: 2017/12/29
 * Time: 上午9:16
 */
interface Store
{

    /**
     * 根据Key 获取字符或数组
     *
     * @param  string|array  $key
     * @return mixed
     */
    public function get($key);

    /**
     * 存储一个缓存,并制定失效时间
     *
     * @param  string  $key
     * @param  mixed   $value
     * @param  float|int  $minutes
     * @return void
     */
    public function put($key, $value, $minutes);



    /**
     * 删除一个缓存
     *
     * @param  string  $key
     * @return bool
     */
    public function forget($key);



    /**
     * 删除所有的缓存
     *
     * @return bool
     */
    public function flush();


    /**
     * 可以获取多个key,如果返回的key里面是没有找到怎么会返回一个null值
     *
     * @param  array  $keys
     * @return array
     */
    public function many(array $keys);



    /**
     *  一次性存放很多的缓存的数组,并指定失效时间
     *
     * @param  array  $values
     * @param  float|int  $minutes
     * @return void
     */
    public function putMany(array $values, $minutes);

    /**
     * 对缓存的key数值增加
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return int|bool
     */
    public function increment($key, $value = 1);

    /**
     * 对缓存的key数值减少
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return int|bool
     */
    public function decrement($key, $value = 1);

    /**
     * 设置这个缓存,不设置失效时间
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return void
     */
    public function forever($key, $value);


    /**
     * 获取缓存的前缀
     *
     * @return string
     */
    public function getPrefix();

}