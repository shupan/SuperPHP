<?php

namespace Super\Database\Concerns;

use Closure;
use Exception;
use Throwable;

/**
 * 事务的操作主要保证ACID , 避免产生数据的差异。
 *
 * Class ManagesTransactions
 * @package Super\Database\Concerns
 */
trait ManagesTransactions
{
    /**
     * 对事务的操作
     *
     * @param  \Closure  $callback
     * @param  int  $attempts
     * @return mixed
     *
     * @throws \Exception|\Throwable
     */
    public function transaction(Closure $callback, $attempts = 1)
    {
        for ($currentAttempt = 1; $currentAttempt <= $attempts; $currentAttempt++) {
            $this->beginTransaction();

            //事务处理成功后自动的提交
            try {
                return tap($callback($this), function ($result) {
                    $this->commit();
                });
            }

            //事务异常需要对其进行异常进行处理
            catch (Exception $e) {
                $this->handleTransactionException(
                    $e, $currentAttempt, $attempts
                );
            } catch (Throwable $e) {
                $this->rollBack();

                throw $e;
            }
        }
    }

    /**
     * 在执行Statement 对事务的异常进行处理
     *
     * @param  \Exception  $e
     * @param  int  $currentAttempt
     * @param  int  $maxAttempts
     * @return void
     *
     * @throws \Exception
     */
    protected function handleTransactionException($e, $currentAttempt, $maxAttempts)
    {

        //对于发现死锁的情况, 事务需要减1 ,并扔出事务的异常信息
        if ($this->causedByDeadlock($e) &&
            $this->transactions > 1) {
            $this->transactions--;

            throw $e;
        }

        // 对事务的处理,需要回滚其状态情况
        $this->rollBack();

        //我们最大尝试次数发现还是死锁,则不进行处理
        if ($this->causedByDeadlock($e) &&
            $currentAttempt < $maxAttempts) {
            return;
        }

        throw $e;
    }

    /**
     * 开始一个事务
     * @todo 暂时不支持对事务的事件处理
     *
     * @return void
     * @throws \Exception
     */
    public function beginTransaction()
    {
        $this->createTransaction();

        $this->transactions++;
    }

    /**
     *  创建一个事务
     * 描述:
     * 对于事务的开启就需要有事务的保存点
     *
     * @return void
     */
    protected function createTransaction()
    {
        if ($this->transactions == 0) {
            try {
                $this->getPdo()->beginTransaction();
            } catch (Exception $e) {
                $this->handleBeginTransactionException($e);
            }
        } elseif ($this->transactions >= 1) {
            $this->createSavepoint();
        }
    }

    /**
     * 创建事务的保存点
     *
     * @return void
     */
    protected function createSavepoint()
    {
        $this->getPdo()->exec(
            'ROLLBACK TO SAVEPOINT '. ($this->transactions +1)
        );
    }

    /**
     * 处理事务的一个异常
     *
     * @param  \Exception  $e
     * @return void
     *
     * @throws \Exception
     */
    protected function handleBeginTransactionException($e)
    {
        if ($this->causedByLostConnection($e)) {
            $this->reconnect();

            $this->pdo->beginTransaction();
        } else {
            throw $e;
        }
    }

    /**
     * 提交事务
     *
     * @return void
     */
    public function commit()
    {
        if ($this->transactions == 1) {
            $this->getPdo()->commit();
        }

        $this->transactions = max(0, $this->transactions - 1);

        $this->fireConnectionEvent('committed');
    }

    /**
     * 回滚事务的操作
     *
     * @todo  暂时没有对失败的事务做事件的处理
     * @param  int|null  $toLevel
     * @return void
     */
    public function rollBack($toLevel = null)
    {
        $toLevel = is_null($toLevel)
                    ? $this->transactions - 1
                    : $toLevel;

        if ($toLevel < 0 || $toLevel >= $this->transactions) {
            return;
        }

        //回滚到制定的事务等级
        $this->performRollBack($toLevel);

        $this->transactions = $toLevel;

    }

    /**
     * 执行一个事务的回滚
     *
     * @param  int  $toLevel
     * @return void
     */
    protected function performRollBack($toLevel)
    {
        if ($toLevel == 0) {
            $this->getPdo()->rollBack();
        } else {
            $this->getPdo()->exec(
                'ROLLBACK TO SAVEPOINT '. ($toLevel +1)
            );
        }
    }

    /**
     * 获取事务的级别
     *
     * @return int
     */
    public function transactionLevel()
    {
        return $this->transactions;
    }
}
