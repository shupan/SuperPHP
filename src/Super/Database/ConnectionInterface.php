<?php

namespace Super\Database;
use Closure;

/**
 * 数据库连接接口
 * 1. 数据的驱动连接
 * 2. 数据的crud操作
 * 3. 数据库的事务支持
 *
 * User: phil.shu
 * Date: 2018/1/1
 * Time: 下午12:40
 */
interface ConnectionInterface
{

    /**
     * 单条数据的查询
     *
     * @param  string  $query
     * @param  array   $bindings
     * @return mixed
     */
    public function selectOne($query, $bindings = []);

    /**
     * 列表集合的查询输出
     *
     * @param  string  $query
     * @param  array   $bindings
     * @return array
     */
    public function select($query, $bindings = []);

    /**
     * 插入数据
     *
     * @param  string  $query
     * @param  array   $bindings
     * @return bool
     */
    public function insert($query, $bindings = []);

    /**
     * 更新数据
     *
     * @param  string  $query
     * @param  array   $bindings
     * @return int
     */
    public function update($query, $bindings = []);

    /**
     * 删除数据
     *
     * @param  string  $query
     * @param  array   $bindings
     * @return int
     */
    public function delete($query, $bindings = []);


    /**
     * 事务回调
     *
     * @param  \Closure  $callback
     * @param  int  $attempts
     * @return mixed
     *
     * @throws \Throwable
     */
    public function transaction(Closure $callback, $attempts = 1);

    /**
     * 开启事务
     *
     * @return void
     */
    public function beginTransaction();

    /**
     * 提交一个事务
     *
     * @return void
     */
    public function commit();

    /**
     * 回滚事务
     *
     * @return void
     */
    public function rollBack();



}