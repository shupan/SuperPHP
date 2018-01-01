<?php
/**
 * User: phil.shu
 * Date: 2018/1/1
 * Time: 下午12:43
 */

namespace Super\Database;


use Closure;

class Connection implements ConnectionInterface
{


    /**
     * 单条数据的查询
     *
     * @param  string $query
     * @param  array $bindings
     * @return mixed
     */
    public function selectOne($query, $bindings = [])
    {
        // TODO: Implement selectOne() method.
    }

    /**
     * 列表集合的查询输出
     *
     * @param  string $query
     * @param  array $bindings
     * @return array
     */
    public function select($query, $bindings = [])
    {
        // TODO: Implement select() method.
    }

    /**
     * 插入数据
     *
     * @param  string $query
     * @param  array $bindings
     * @return bool
     */
    public function insert($query, $bindings = [])
    {
        // TODO: Implement insert() method.
    }

    /**
     * 更新数据
     *
     * @param  string $query
     * @param  array $bindings
     * @return int
     */
    public function update($query, $bindings = [])
    {
        // TODO: Implement update() method.
    }

    /**
     * 删除数据
     *
     * @param  string $query
     * @param  array $bindings
     * @return int
     */
    public function delete($query, $bindings = [])
    {
        // TODO: Implement delete() method.
    }

    /**
     * 事务回调
     *
     * @param  \Closure $callback
     * @param  int $attempts
     * @return mixed
     *
     * @throws \Throwable
     */
    public function transaction(Closure $callback, $attempts = 1)
    {
        // TODO: Implement transaction() method.
    }

    /**
     * 开启事务
     *
     * @return void
     */
    public function beginTransaction()
    {
        // TODO: Implement beginTransaction() method.
    }

    /**
     * 提交一个事务
     *
     * @return void
     */
    public function commit()
    {
        // TODO: Implement commit() method.
    }

    /**
     * 回滚事务
     *
     * @return void
     */
    public function rollBack()
    {
        // TODO: Implement rollBack() method.
    }
}