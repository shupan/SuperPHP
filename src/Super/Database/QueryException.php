<?php

namespace Super\Database;

use PDOException;
use Super\Support\Str;

class QueryException extends PDOException
{
    /**
     * SqL 信息
     *
     * @var string
     */
    protected $sql;

    /**
     * 绑定的参数信息
     *
     * @var array
     */
    protected $bindings;

    /**
     * 异常的实例
     *
     * @param  string  $sql
     * @param  array  $bindings
     * @param  \Exception $previous
     * @return void
     */
    public function __construct($sql, array $bindings, $previous)
    {
        parent::__construct('', 0, $previous);

        $this->sql = $sql;
        $this->bindings = $bindings;
        $this->code = $previous->getCode();
        $this->message = $this->formatMessage($sql, $bindings, $previous);

        if ($previous instanceof PDOException) {
            $this->errorInfo = $previous->errorInfo;
        }
    }

    /**
     * 格式化异常的信息
     *
     * @param  string  $sql
     * @param  array  $bindings
     * @param  \Exception $previous
     * @return string
     */
    protected function formatMessage($sql, $bindings, $previous)
    {
        return $previous->getMessage().' (SQL: '.Str::replaceArray('?', $bindings, $sql).')';
    }

    /**
     * 获取SQL的操作
     *
     * @return string
     */
    public function getSql()
    {
        return $this->sql;
    }

    /**
     * 获取绑定的参数
     *
     * @return array
     */
    public function getBindings()
    {
        return $this->bindings;
    }
}
