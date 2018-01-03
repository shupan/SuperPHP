<?php
/**
 * User: phil.shu
 * Date: 2018/1/1
 * Time: 下午12:44
 */

namespace Super\Database;


/**
 * 支持多个数据库执行管理和操作
 * Class DatabaseManager
 * @package Super\Database
 */
class DatabaseManager
{
    /**
     * The application instance.
     *
     * @var \Super\Foundation\Application
     */
    protected $app;

    /**
     * The database connection factory instance.
     *
     * @var \Super\Database\Connectors\ConnectionFactory
     */
    protected $factory;


    /**
     * 获取活跃的连接实例
     * @var array
     */
    protected $connections = [];


    /**
     * 根据链接的名称获取链接的实例
     * @param null $name
     * @return mixed|Connection
     */
    public function connection($name = null)
    {

        if (!isset($this->connections[$name])) {
            return $this->connections[$name];
        } else {
            return $this->makeConnection($name);
        }
    }


    /**
     * 创建连接对象
     * @param string $name
     * @return \Super\Database\Connection
     */
    protected function makeConnection($name)
    {

        $this->connections[$name] = '';

        return '';
    }


    /**
     * 根据实例对象名称获取app应用的配置信息
     * @param $name
     * @return array
     */
    protected  function getConfig($name)
    {

        return [];
    }

    /**
     * 动态调用Connection 的方法
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->connection()->$method(...$parameters);
    }

}