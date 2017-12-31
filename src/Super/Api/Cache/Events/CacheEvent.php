<?php

namespace Super\Cache\Events;

abstract class CacheEvent
{
    /**
     * 缓存事件Key
     *
     * @var string
     */
    public $key;

    /**
     * 缓存事件打的标签
     *
     * @var array
     */
    public $tags;

    /**
     * 创建一个缓存事件
     *
     * @param  string  $key
     * @param  array  $tags
     * @return void
     */
    public function __construct($key, array $tags = [])
    {
        $this->key = $key;
        $this->tags = $tags;
    }

    /**
     * 设置缓存的事件标签
     *
     * @param  array  $tags
     * @return $this
     */
    public function setTags($tags)
    {
        $this->tags = $tags;

        return $this;
    }
}
