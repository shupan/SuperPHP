<?php

namespace Super\Api\Cache;

/**
 * 根据缓存对象名称来获取缓存
 * Interface Factory
 * @package Super\Api\Cache
 */
interface Factory
{
    /**
     * 获取缓存的名称
     *
     * @param  string|null  $name
     * @return mixed
     */
    public function store($name = null);
}
