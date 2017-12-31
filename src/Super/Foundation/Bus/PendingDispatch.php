<?php

namespace Super\Foundation\Bus;

class PendingDispatch
{
    /**
     * 新建一个job
     *
     * @param  mixed  $job
     * @return void
     */
    public function __construct($job)
    {
        $this->job = $job;
    }

    /**
     * 对事件的链接
     *
     * @param  string|null  $connection
     * @return $this
     */
    public function onConnection($connection)
    {
        $this->job->onConnection($connection);

        return $this;
    }

    /**
     * 把job放进队列中
     *
     * @param  string|null  $queue
     * @return $this
     */
    public function onQueue($queue)
    {
        $this->job->onQueue($queue);

        return $this;
    }

    /**
     * 对job设置延长事件
     *
     * @param  \DateTime|int|null  $delay
     * @return $this
     */
    public function delay($delay)
    {
        $this->job->delay($delay);

        return $this;
    }

    /**
     * 销毁这个job操作
     *
     * @return void
     */
    public function __destruct()
    {
        dispatch($this->job);
    }
}
