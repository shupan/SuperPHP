<?php

namespace Super\Api\Redis;

interface Factory
{
    /**
     * Get a Redis connection by name.
     *
     * @param  string  $name
     * @return \Super\Redis\Connections\Connection
     */
    public function connection($name = null);
}
