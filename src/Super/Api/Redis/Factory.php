<?php

namespace Super\Api\Redis;

interface Factory
{
    /**
     * 获取Redis 根据Redis的名称
     *
     * @param  string  $name
     * @return \Super\Redis\Connections\Connection
     */
    public function connection($name = null);
}
