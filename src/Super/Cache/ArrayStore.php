<?php

namespace Super\Cache;

use Super\Api\Cache\Store;

/**
 * User: phil.shu
 * Date: 2017/12/29
 * Time: 上午9:30
 */
class ArrayStore implements Store
{

    use GetMultipleKeys;
    /**
     * 数值的存储
     * @var array
     */
    protected $storage = [];

    /**
     * 根据Key 获取字符或数组
     *
     * @param  string|array $key
     * @return mixed
     */
    public function get($key)
    {
        if (array_key_exists($key, $this->storage)) {
            return $this->storage[$key];
        }
        return null;
    }

    /**
     * 存储一个缓存,并制定失效时间
     *
     * @param  string $key
     * @param  mixed $value
     * @param  float|int $minutes
     * @return void
     */
    public function put($key, $value, $minutes)
    {
        $this->storage[$key] = $value;
    }

    /**
     * 删除一个缓存
     *
     * @param  string $key
     * @return bool
     */
    public function forget($key)
    {
        unset($this->storage[$key]);
    }

    /**
     * 删除所有的缓存
     *
     * @return bool
     */
    public function flush()
    {
        $this->storage = [];
        return true;
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
        $this->storage[$key] = !isset($this->storage[$key]) ?
            $value : ((int)$this->storage[$key]) + $value;
        return $this->storage[$key];
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
        $this->increment($key, -1 * $value);
        return $this->storage[$key];
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
        $this->put($key, $value, 0);
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