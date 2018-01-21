<?php
/**
 * User: phil.shu
 * Date: 2018/1/21
 * Time: 下午9:06
 */

namespace Super\Support\Structure;


use Super\Support\Structure\Exception\QueueException;

class ArrayQueue implements Queue
{

    /**
     * 数组的大小
     * @var array
     */
    protected $arr = [];

    /**
     * 最大的长度
     * @var
     */
    protected $maxLength;

    /**
     * 当前的队列长度
     * @var int
     */
    protected $currentLength = 0;


    /**
     * 设置队列的长度
     * ArrayQueue constructor.
     * @param $maxLength
     */
    public function __construct($maxLength)
    {

        $this->maxLength = $maxLength;
    }

    /**
     * 加入队列
     * @param $val
     * @return mixed
     * @throws QueueException
     */
    public function push($val)
    {
        if ($this->getLength() >= $this->getMaxLength()) {
            throw new QueueException("Queue is fill, please set great more queue length");
        }
        $this->arr[] = $val;
        $this->currentLength = $this->currentLength + 1;
        return true;
    }

    /**
     * 从队列中弹出来
     * @return mixed
     * @throws  QueueException
     */
    public function pop()
    {
        if ($this->currentLength - 1 < 0) {
            throw new QueueException("Queue has not any info");
        }
        $this->currentLength = $this->currentLength - 1;
        return array_shift($this->arr);
    }

    /**
     * 获取队列的最大长度
     * @return int
     */
    public function getMaxLength()
    {

        return $this->maxLength;
    }

    public function setMaxLength($maxLength)
    {
        if ($maxLength <= $this->getLength()) {

            throw new QueueException("Queue max length must great than " . $this->getLength());
        }
        $this->maxLength = $maxLength;
    }

    /**
     * 获取队列的使用长度
     * @return mixed
     */
    public function getLength()
    {

        return $this->currentLength;
    }
}