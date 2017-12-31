<?php

namespace Super\Cache;

use Closure;
use DateTime;
use ArrayAccess;
use Carbon\Carbon;
use BadMethodCallException;
use Super\Cache\Events\CacheHit;
use Super\Api\Cache\Store;
use Super\Cache\Events\KeyWritten;
use Super\Cache\Events\CacheMissed;
use Super\Support\Traits\Macroable;
use Super\Cache\Events\KeyForgotten;
use Super\Api\Events\Dispatcher;
use Super\Api\Cache\Repository as CacheContract;

/**
 * 缓存仓库
 * 描述:
 * 把缓存放到仓库中,并且对缓存的get,put,delete等触发相应的事件处理。
 * @mixin \Super\Api\Cache\Store
 */
class RepositoryImpl implements CacheContract, ArrayAccess
{
    use Macroable {
        __call as macroCall;
    }

    /**
     * 缓存的存储实现
     *
     * @var \Super\Api\Cache\Store
     */
    protected $store;

    /**
     * 事件的触发
     *
     * @var \Super\Api\Events\Dispatcher
     */
    protected $events;

    /**
     * 默认失效的时间60m
     *
     * @var float|int
     */
    protected $default = 60;

    /**
     * 创建缓存存储库实例
     *
     * @param  \Super\Api\Cache\Store  $store
     * @return void
     */
    public function __construct(Store $store)
    {
        $this->store = $store;
    }

    /**
     * 检测是否存在缓存
     *
     * @param  string  $key
     * @return bool
     */
    public function has($key)
    {
        return ! is_null($this->get($key));
    }

    /**
     * 从缓存库中查下缓存对象
     *
     * @param  string  $key
     * @param  mixed   $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        if (is_array($key)) {
            return $this->many($key);
        }

        $value = $this->store->get($this->itemKey($key));

        // If we could not find the cache value, we will fire the missed event and get
        // the default value for this cache value. This default could be a callback
        // so we will execute the value function which will resolve it if needed.
        if (is_null($value)) {
            $this->event(new CacheMissed($key));

            $value = value($default);
        } else {
            $this->event(new CacheHit($key, $value));
        }

        return $value;
    }

    /**
     * 根据多个Key 获取缓存的数据
     *
     * @param  array  $keys
     * @return array
     */
    public function many(array $keys)
    {
        $values = $this->store->many(collect($keys)->map(function ($value, $key) {
            return is_string($key) ? $key : $value;
        })->values()->all());

        return collect($values)->map(function ($value, $key) use ($keys) {
            return $this->handleManyResult($keys, $key, $value);
        })->all();
    }

    /**
     * 对于多个时间是否做事件的处理
     *
     * @param  array  $keys
     * @param  string  $key
     * @param  mixed  $value
     * @return mixed
     */
    protected function handleManyResult($keys, $key, $value)
    {
        // If we could not find the cache value, we will fire the missed event and get
        // the default value for this cache value. This default could be a callback
        // so we will execute the value function which will resolve it if needed.
        if (is_null($value)) {
            $this->event(new CacheMissed($key));

            return isset($keys[$key]) ? value($keys[$key]) : null;
        }

        // If we found a valid value we will fire the "hit" event and return the value
        // back from this function. The "hit" event gives developers an opportunity
        // to listen for every possible cache "hit" throughout this applications.
        $this->event(new CacheHit($key, $value));

        return $value;
    }

    /**
     * 查询一个缓存,并删除该对象
     *
     * @param  string  $key
     * @param  mixed   $default
     * @return mixed
     */
    public function pull($key, $default = null)
    {
        return tap($this->get($key, $default), function ($value) use ($key) {
            $this->forget($key);
        });
    }

    /**
     * 存储一个对象到缓存中
     *
     * @param  string  $key
     * @param  mixed   $value
     * @param  \DateTime|float|int  $minutes
     * @return void
     */
    public function put($key, $value, $minutes = null)
    {
        if (is_array($key)) {
            return $this->putMany($key, $value);
        }

        if (! is_null($minutes = $this->getMinutes($minutes))) {
            $this->store->put($this->itemKey($key), $value, $minutes);

            $this->event(new KeyWritten($key, $value, $minutes));
        }
    }

    /**
     * 存储多个项到缓存中
     *
     * @param  array  $values
     * @param  float|int  $minutes
     * @return void
     */
    public function putMany(array $values, $minutes)
    {
        if (! is_null($minutes = $this->getMinutes($minutes))) {
            $this->store->putMany($values, $minutes);

            foreach ($values as $key => $value) {
                $this->event(new KeyWritten($key, $value, $minutes));
            }
        }
    }

    /**
     * 对于缓存不存在的情况,增加一个缓存项
     *
     * @param  string  $key
     * @param  mixed   $value
     * @param  \DateTime|float|int  $minutes
     * @return bool
     */
    public function add($key, $value, $minutes)
    {
        if (is_null($minutes = $this->getMinutes($minutes))) {
            return false;
        }

        // If the store has an "add" method we will call the method on the store so it
        // has a chance to override this logic. Some drivers better support the way
        // this operation should work with a total "atomic" implementation of it.
        if (method_exists($this->store, 'add')) {
            return $this->store->add(
                $this->itemKey($key), $value, $minutes
            );
        }

        // If the value did not exist in the cache, we will put the value in the cache
        // so it exists for subsequent requests. Then, we will return true so it is
        // easy to know if the value gets added. Otherwise, we will return false.
        if (is_null($this->get($key))) {
            $this->put($key, $value, $minutes);

            return true;
        }

        return false;
    }

    /**
     * 缓存计算增加
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return int|bool
     */
    public function increment($key, $value = 1)
    {
        return $this->store->increment($key, $value);
    }

    /**
     * 缓存的计算的减少
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return int|bool
     */
    public function decrement($key, $value = 1)
    {
        return $this->store->decrement($key, $value);
    }

    /**
     * 设置缓存为永久
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return void
     */
    public function forever($key, $value)
    {
        $this->store->forever($this->itemKey($key), $value);

        $this->event(new KeyWritten($key, $value, 0));
    }

    /**
     * 存储一个缓存,并看是否需要触发回调
     *
     * @param  string  $key
     * @param  \DateTime|float|int  $minutes
     * @param  \Closure  $callback
     * @return mixed
     */
    public function remember($key, $minutes, Closure $callback)
    {
        $value = $this->get($key);

        // If the item exists in the cache we will just return this immediately and if
        // not we will execute the given Closure and cache the result of that for a
        // given number of minutes so it's available for all subsequent requests.
        if (! is_null($value)) {
            return $value;
        }

        $this->put($key, $value = $callback(), $minutes);

        return $value;
    }

    /**
     * 获取一个缓存,并看是否需要触发回调
     *
     * @param  string   $key
     * @param  \Closure  $callback
     * @return mixed
     */
    public function sear($key, Closure $callback)
    {
        return $this->rememberForever($key, $callback);
    }

    /**
     * 获取一个不失效缓存,并看是否需要触发回调
     *
     * @param  string   $key
     * @param  \Closure  $callback
     * @return mixed
     */
    public function rememberForever($key, Closure $callback)
    {
        $value = $this->get($key);

        // If the item exists in the cache we will just return this immediately and if
        // not we will execute the given Closure and cache the result of that for a
        // given number of minutes so it's available for all subsequent requests.
        if (! is_null($value)) {
            return $value;
        }

        $this->forever($key, $value = $callback());

        return $value;
    }

    /**
     * 删除一个缓存
     *
     * @param  string  $key
     * @return bool
     */
    public function forget($key)
    {
        return tap($this->store->forget($this->itemKey($key)), function () use ($key) {
            $this->event(new KeyForgotten($key));
        });
    }

//    /**
//     * Tag 的操作
//     *
//     * @param  array|mixed  $names
//     * @return \Super\Cache\TaggedCache
//     *
//     * @throws \BadMethodCallException
//     */
//    public function tags($names)
//    {
//        if (! method_exists($this->store, 'tags')) {
//            throw new BadMethodCallException('This cache store does not support tagging.');
//        }
//
//        $cache = $this->store->tags($names);
//
//        if (! is_null($this->events)) {
//            $cache->setEventDispatcher($this->events);
//        }
//
//        return $cache->setDefaultCacheTime($this->default);
//    }

    /**
     * 获取缓存项
     *
     * @param  string  $key
     * @return string
     */
    protected function itemKey($key)
    {
        return $key;
    }

    /**
     * 获取缓存默认的时间
     *
     * @return float|int
     */
    public function getDefaultCacheTime()
    {
        return $this->default;
    }

    /**
     * 设置缓存的默认时间
     *
     * @param  float|int  $minutes
     * @return $this
     */
    public function setDefaultCacheTime($minutes)
    {
        $this->default = $minutes;

        return $this;
    }

    /**
     * 获取缓存的存储实现
     *
     * @return \Super\Api\Cache\Store
     */
    public function getStore()
    {
        return $this->store;
    }

    /**
     * 发射一个事件到缓存中
     *
     * @param  string  $event
     * @return void
     */
    protected function event($event)
    {
        if (isset($this->events)) {
            $this->events->dispatch($event);
        }
    }

    /**
     * 设置缓存时间的调用
     *
     * @param  \Super\Api\Events\Dispatcher  $events
     * @return void
     */
    public function setEventDispatcher(Dispatcher $events)
    {
        $this->events = $events;
    }

    /**
     * 判断一个缓存值是否存在
     *
     * @param  string  $key
     * @return bool
     */
    public function offsetExists($key)
    {
        return $this->has($key);
    }

    /**
     * 查询缓存
     *
     * @param  string  $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        return $this->get($key);
    }

    /**
     * 设置缓存,并设置默认时间60min
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return void
     */
    public function offsetSet($key, $value)
    {
        $this->put($key, $value, $this->default);
    }

    /**
     * 删除不需要的缓存
     *
     * @param  string  $key
     * @return void
     */
    public function offsetUnset($key)
    {
        $this->forget($key);
    }

    /**
     * 获取缓存有效的时间
     *
     * @param  \DateTime|float|int  $duration
     * @return float|int|null
     */
    protected function getMinutes($duration)
    {
        if ($duration instanceof DateTime) {
            $duration = Carbon::now()->diffInSeconds(Carbon::instance($duration), false) / 60;
        }

        return (int) ($duration * 60) > 0 ? $duration : null;
    }

    /**
     * 缓存的动态调用
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if (static::hasMacro($method)) {
            return $this->macroCall($method, $parameters);
        }

        return $this->store->$method(...$parameters);
    }

    /**
     * 复制资源库实例
     *
     * @return void
     */
    public function __clone()
    {
        $this->store = clone $this->store;
    }
}
