<?php
/**
 * User: phil.shu
 * Date: 2018/1/1
 * Time: 下午12:43
 */

namespace Super\Database;


use Closure;
use Exception;
use PDO;
use PDOStatement;
use Super\Database\Concerns\ManagesTransactions;
use Super\Support\Str;

class Connection implements ConnectionInterface
{

    use ManagesTransactions;

    /**
     * PDO 对象
     * @var  \PDO
     */
    protected $pdo = null;

    /**
     * 连接的数据库名称
     * @var string
     */
    protected $database = '';

    /**
     * 表前缀
     * @var string
     */
    protected $tablePrefix = '';

    /**
     * 事务活跃的数量
     * @var int
     */
    protected  $transactions = 0;


    /**
     * 构建PDO 连接器
     * 描述:
     *  php 在5.1版本之后pdo已经统一了数据库接口的方法
     * @param $pdo \PDOStatement  对象
     * @param string $database
     * @param string $tablePrefix
     */
    public function __construct($pdo, $database = '', $tablePrefix = '')
    {
        $this->pdo = $pdo;
        $this->database = $database;
        $this->tablePrefix = $tablePrefix;
    }


    /**
     * 单条数据的查询
     *
     * @param  string $query
     * @param  array $bindings
     * @return mixed
     */
    public function selectOne($query, $bindings = [])
    {
        $rows = $this->select($query, $bindings);

        //获取结果集的第一条记录
        return array_shift($rows);
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

        $result = $this->run($query, $bindings, function ($query, $bindings) {

            $statement = $this->getPdo()->prepare($query);

            $this->bindValues($statement, $bindings);

            $statement->execute();

            //返回所有结果集
            return $statement->fetchAll();
        });

        return $result;
    }


    /**
     * 绑定多个多个参数的信息
     * @param PDOStatement $statement
     * @param $bindings
     */
    protected function bindValues(PDOStatement $statement, $bindings)
    {

        //对statement填充必要的参数信息
        foreach ($bindings as $key => $val) {

            //对日期格式的处理
            if($val instanceof  \DateTimeInterface)
            {
                $val = $val->format('Y-m-d H:i:s');
            }else if($val === false)
            {
                $val = 0;
            }

            //如果是参数序号是从1开始的,如果是key字符串则不需要考
            $statement->bindValue(
                is_string($key) ? $key : $key + 1,
                is_string($val) ? PDO::PARAM_STR : PDO::PARAM_INT
            );
        }
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
        return $this->statement($query, $bindings);
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
        return $this->statement($query, $bindings);
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
        return $this->statement($query, $bindings);
    }
    


    /**
     * 执行一条 SQL 语句，并返回受影响的行数
     * @param $query
     * @param array $bindings
     * @return bool
     */
    public function statement($query, $bindings = [])
    {
        return $this->run(
            $query, $bindings,
            function ($query, $bindings) {
                $statement = $this->getPdo()->prepare($query);

                $this->bindValues($statement, $bindings);
                //执行返回结构
                return $statement->execute();
            }
        );
    }


    /**
     * 对执行调用的Statement 结果返回和异常的处理
     * @param $query
     * @param $bindings
     * @param $callback
     * @return mixed
     */
    protected function run($query, $bindings, $callback)
    {

        try {
            $result = $callback($query, $bindings);

        } catch (\Exception $e) {
            //throw new QueryException($query, $bindings, $e);

            //进行异常信息的注册
            if ($this->causedByLostConnection($e)) {
                //网络异常可以重新连接下
                $this->reconnect();
                return $callback($query, $bindings);
            }
        }
        return $result;
    }

    /**
     * 获取PDO对象
     *
     * @return \PDO
     */
    public function getPdo()
    {
//        if ($this->pdo instanceof Closure) {
//            return $this->pdo = call_user_func($this->pdo);
//        }

        return $this->pdo;
    }

    public function setPdo($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * 查询SQL重连接机制
     * 待考虑实现
     */
    public function reconnect()
    {

    }

    /**
     * 断开连接
     */
    public function disconnect()
    {

        $this->setPdo(null);
    }

    /**
     *  根据数据库返回的常用提示信息来判断是否需要重连
     *
     * @param  \Exception $e
     * @return bool
     */
    protected function causedByLostConnection(Exception $e)
    {
        $message = $e->getMessage();

        return Str::contains($message, [
            'server has gone away',
            'no connection to the server',
            'Lost connection',
            'is dead or not enabled',
            'Error while sending',
            'decryption failed or bad record mac',
            'server closed the connection unexpectedly',
            'SSL connection has been closed unexpectedly',
            'Error writing data to the connection',
            'Resource deadlock avoided',
            'Transaction() on null',
        ]);
    }

    /**
     *  检查连接是否存在死锁的情况,如果存在这进行对应的处理
     *
     * @param  \Exception  $e
     * @return bool
     */
    protected function causedByDeadlock(Exception $e)
    {
        $message = $e->getMessage();

        return Str::contains($message, [
            'Deadlock found when trying to get lock',
            'deadlock detected',
            'The database file is locked',
            'database is locked',
            'database table is locked',
            'A table in the database is locked',
            'has been chosen as the deadlock victim',
            'Lock wait timeout exceeded; try restarting transaction',
        ]);
    }

    /**
     * @return int
     */
    public function getTransactions()
    {
        return $this->transactions;
    }

    /**
     * @param int $transactions
     */
    public function setTransactions($transactions)
    {
        $this->transactions = $transactions;
    }


}