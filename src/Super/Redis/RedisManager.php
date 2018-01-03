<?php

namespace Super\Redis;

use Super\Api\Redis\Factory;
use Super\Redis\Connections\PhpRedisConnection;
use Super\Redis\Connections\PredisConnection;
use Super\Redis\Connector\PhpredisConnector;
use Super\Redis\Connector\PredisConnector;
use Symfony\Component\Process\Exception\InvalidArgumentException;

/**
 * Redis的管理器
 * 描述:
 * 管理多个Redis注册,启动,关闭等操作
 *
 * User: phil.shu
 * Date: 2017/12/31
 * Time: 下午3:48
 */
class RedisManager implements Factory
{

    /**
     * redis 连接的驱动
     * @var null
     */
    private $driver = null;

    /**
     * redis 的连接配置
     * @var null
     */
    private $config = null;

    /**
     * 通过驱动和配置可以启动Redis操作
     * RedisManager constructor.
     * @param $driver
     * @param arrray $config
     */
    public function __construct($driver, array $config)
    {

        $this->driver = $driver;
        $this->config = $config;

    }

    /**
     * 获取Redis 根据Redis 名称
     * 1. 获取配置信息
     *
     * @param  string $name
     * @return \Super\Redis\Connections\Connection
     */
    public function connection($name = null)
    {

        if (isset($this->config, $name)) {
            return $this->getConnector();
        }
    }

    /**
     * 通过连接名称获取Redis对象
     * @param $name
     * @return string
     */
    public function resolve($name)
    {

        $name = $name ? $name : 'default';
        $options = isset($this->config['options']) ? $this->config['options'] : [];
        if (isset($this->config[$name])) {
            return $this->getConnector()->connect($this->config, $options);
        }

        if (isset($this->config['clusters'][$name])) {
            return $this->resolveCluster($name);
        }

        throw new InvalidArgumentException(
            "Redis connection [{$name}] not configured."
        );
    }


    /**
     * 通过Redis名称获取Redis的集群
     * @param $name
     * @return \Predis\Client|\Redis
     */
    public function resolveCluster($name)
    {
        $options = isset($this->config['options']) ? $this->config['options'] : [];

        $clusterOptions = isset($this->config['clusters.options']) ? $this->config['clusters.options'] : [];

        return $this->getConnector()->connectCluster(
            $this->config['clusters'][$name], $clusterOptions, $options
        );
    }

    /**
     *  获取不同的连接器
     * 描述:
     * php redis扩展链接redis 服务主要两种方式:
     * 1. Predis可以实现redis一致性行算法,也支持tcp/ip协议的方式,灵活方便扩展
     * 2. phpredis 是php的二进制扩展包,性能要高于predis ,但是效率要比Predis高。
     * 这个版本支持作为Session的Handler。这个扩展的有点在于无需加载任何外部文件，使用比较方便。缺点在于难于扩展，
     *
     */
    public function getConnector()
    {
        if ($this->driver == 'predis') {

            return new PredisConnector();
        } else if ($this->driver == 'phpredis') {

            return new PhpredisConnector();
        } else {

            throw new InvalidArgumentException(
                "$this->driver php driver is not support!"
            );
        }
    }


    /**
     * 可以传递参数调用redis的默认连接操作
     * @param $method
     * @param $arguments
     */
    public function __call($method, $arguments)
    {
        $this->connection()->{$method}(...$arguments);
    }
}