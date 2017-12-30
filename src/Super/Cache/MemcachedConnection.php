<?php

namespace Super\Cache;
use Memcached;

/**
 * 描述:
 *    支持多个Memcached的服务器的连接操作.
 * 并提供统一的Memcached的管理机制
 * User: phil.shu
 * Date: 2017/12/29
 * Time: 上午9:30
 */
class MemcachedConnection
{

    /**
     * 建立一个Connection 和多个 Connection 的连接
     * @param array $servers
     * @param null $connectionId
     * @param array $options
     * @param array $credentials
     * @return Memcached
     */
    public function connect(array $servers, $connectionId = null, array $options = [], array $credentials = [])
    {
        $memcached = $this->getMemcached(
            $connectionId, $credentials, $options
        );

        if (! $memcached->getServerList()) {

            //把所有需要配置的Memcache链接放到ServerList中,统一管理
            foreach ($servers as $server) {
                $memcached->addServer(
                    $server['host'], $server['port'], $server['weight']
                );
            }
        }

        return $memcached;
    }

    /**
     * 获取Memcached实例
     * @param $connectionId
     * @param array $credentials
     * @param array $options
     * @return Memcached
     */
    protected function getMemcached($connectionId, array $credentials, array $options)
    {
        $memcached = $this->createMemcachedInstance($connectionId);

        if (count($credentials) == 2) {
            $this->setCredentials($memcached, $credentials);
        }

        if (count($options)) {
            $memcached->setOptions($options);
        }

        return $memcached;
    }


    /**
     * 支持Memcache的实例创建,并且根据connectionId作为Memcached获取
     * @param $connectionId
     * @return Memcached
     * @throws \Exception
     */
    protected function createMemcachedInstance($connectionId)
    {
        if(!class_exists("Memcached")){
            throw new \Exception("Memcached php extension is not found ");
        }
        return empty($connectionId) ? new Memcached : new Memcached($connectionId);
    }


    /**
     * 支持对Memcache的SSL 协议的方式连接,提高数据传输的安全性
     * @param $memcached
     * @param $credentials 支持
     */
    protected function setCredentials($memcached, $credentials)
    {
        list($username, $password) = $credentials;

        $memcached->setOption(Memcached::OPT_BINARY_PROTOCOL, true);

        $memcached->setSaslAuthData($username, $password);
    }

}