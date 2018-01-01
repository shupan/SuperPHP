<?php

namespace Super\Redis\Connector;

/**
 *
 * 使用Predis 客户端进行连接
 * 描述:
 * 1. 支持对单个redis连接
 * 2. 支持对redis集群进行连接
 *
 * User: phil.shu
 * Date: 2018/1/1
 * Time: 下午12:17
 */
class PredisConnector
{


    /**
     * 获取Predis Client 的连接
     * @param array $config
     * @param $options
     * @return \Predis\Client
     */
    public function connect(array $config, $options)
    {

        $param = array_merge($config , $options , [
            'timeout' => 10.0
        ]);

        return new \Predis\Client($param , $options);

    }

    /**
     * 对Redis 的集群进行启动
     *
     * @param array $config
     * @param $clusterOptions
     * @param $options
     * @return \Predis\Client
     */
    public function connectCluster(array $config, $clusterOptions, $options)
    {

        $param = array_merge($config , $options , $clusterOptions,  [
            'timeout' => 10.0
        ]);
        return new \Predis\Client($param , $options);
    }
}