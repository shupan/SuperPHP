<?php

namespace Super\Cache;

use Closure;
use Super\Support\Arr;
use InvalidArgumentException;
use Super\Api\Cache\Store;
use Super\Api\Cache\Factory as FactoryContract;
use Super\Api\Events\Dispatcher as DispatcherContract;


class CacheManager implements FactoryContract
{
    /**
     * 应用的实例
     *
     * @var \Super\Foundation\Application
     */
    protected $app;

    /**
     * 多个缓存存储实体
     *
     * @var array
     */
    protected $stores = [];

    /**
     * 自定义驱动的注册
     *
     * @var array
     */
    protected $customCreators = [];

    /**
     * 创建缓存管理器实例
     *
     * @param  \Super\Foundation\Application  $app
     * @return void
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * 根据缓存名称获取缓存实例
     *
     * @param  string|null  $name
     * @return mixed
     */
    public function store($name = null)
    {
        $name = $name ?: $this->getDefaultDriver();

        return $this->stores[$name] = $this->get($name);
    }

    /**
     * 跟缓存实例配置驱动器
     *
     * @param  string  $driver
     * @return mixed
     */
    public function driver($driver = null)
    {
        return $this->store($driver);
    }

    /**
     * 获取本地的缓存仓库
     *
     * @param  string  $name
     * @return \Super\Api\Cache\Repository
     */
    protected function get($name)
    {
        return isset($this->stores[$name]) ? $this->stores[$name] : $this->resolve($name);
    }

    /**
     * 根据名称获取Repsitory库信息
     *
     * @param  string  $name
     * @return \Super\Api\Cache\Repository
     *
     * @throws \InvalidArgumentException
     */
    protected function resolve($name)
    {
        $config = $this->getConfig($name);

        if (is_null($config)) {
            throw new InvalidArgumentException("Cache store [{$name}] is not defined.");
        }

        if (isset($this->customCreators[$config['driver']])) {
            return $this->callCustomCreator($config);
        } else {
            $driverMethod = 'create'.ucfirst($config['driver']).'Driver';

            if (method_exists($this, $driverMethod)) {
                return $this->{$driverMethod}($config);
            } else {
                throw new InvalidArgumentException("Driver [{$config['driver']}] is not supported.");
            }
        }
    }

    /**
     * 获取自定义的驱动配置
     *
     * @param  array  $config
     * @return mixed
     */
    protected function callCustomCreator(array $config)
    {
        return $this->customCreators[$config['driver']]($this->app, $config);
    }

    /**
     * 获取数组缓存的驱动
     *
     * @return \Super\Cache\ArrayStore
     */
    protected function createArrayDriver()
    {
        return $this->repository(new ArrayStore);
    }

    /**
     * 创建文件的驱动仓库
     *
     * @param  array  $config
     * @return \Super\Cache\FileStore
     */
    protected function createFileDriver(array $config)
    {
        return $this->repository(new FileStore($this->app['files'], $config['path']));
    }

    /**
     * 创建mecached的驱动仓库
     *
     * @param  array  $config
     * @return \Super\Cache\MemcachedStore
     */
    protected function createMemcachedDriver(array $config)
    {
        $prefix = $this->getPrefix($config);

        $memcached = $this->app['memcached.connector']->connect(
            $config['servers'],
            array_get($config, 'persistent_id'),
            array_get($config, 'options', []),
            array_filter(array_get($config, 'sasl', []))
        );

        return $this->repository(new MemcachedStore($memcached, $prefix));
    }


    /**
     * 创建基于Redis的缓存仓库
     *
     * @param  array  $config
     * @return \Super\Cache\RedisStore
     */
    protected function createRedisDriver(array $config)
    {
        $redis = $this->app['redis'];

        $connection = Arr::get($config, 'connection', 'default');

        return $this->repository(new RedisStore($redis, $this->getPrefix($config), $connection));
    }

    /**
     * 创建基于数据库的缓存仓库
     *
     * @param  array  $config
     * @return \Super\Cache\DatabaseStore
     */
    protected function createDatabaseDriver(array $config)
    {
        $connection = $this->app['db']->connection(Arr::get($config, 'connection'));

        return $this->repository(
            new DatabaseStore(
                $connection, $this->app['encrypter'], $config['table'], $this->getPrefix($config)
            )
        );
    }

    /**
     * 根据缓存实体获取缓存仓库
     *
     * @param  \Super\Api\Cache\Store  $store
     * @return \Super\Cache\Repository
     */
    public function repository(Store $store)
    {
        $repository = new Repository($store);

        if ($this->app->bound(DispatcherContract::class)) {
            $repository->setEventDispatcher(
                $this->app[DispatcherContract::class]
            );
        }

        return $repository;
    }

    /**
     * 获取缓存的前缀
     *
     * @param  array  $config
     * @return string
     */
    protected function getPrefix(array $config)
    {
        return Arr::get($config, 'prefix') ?: $this->app['config']['cache.prefix'];
    }

    /**
     * 获取缓存的配置
     *
     * @param  string  $name
     * @return array
     */
    protected function getConfig($name)
    {
        return $this->app['config']["cache.stores.{$name}"];
    }

    /**
     * 获取缓存的驱动配置
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return $this->app['config']['cache.default'];
    }

    /**
     * 设置缓存的默认驱动
     *
     * @param  string  $name
     * @return void
     */
    public function setDefaultDriver($name)
    {
        $this->app['config']['cache.default'] = $name;
    }

    /**
     * 注册一个驱动事件,并绑定
     *
     * @param  string    $driver
     * @param  \Closure  $callback
     * @return $this
     */
    public function extend($driver, Closure $callback)
    {
        $this->customCreators[$driver] = $callback->bindTo($this, $this);

        return $this;
    }

    /**
     * 动态调用默认的缓存驱动
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->store()->$method(...$parameters);
    }
}
