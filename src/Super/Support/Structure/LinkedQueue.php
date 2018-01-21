<?php
/**
 * User: phil.shu
 * Date: 2018/1/21
 * Time: 下午9:07
 */

namespace Super\Support\Structure;


use Super\Support\Structure\Exception\QueueException;

class LinkedQueue implements Queue
{

    /**
     * 最大的长度
     * @var int
     */
    protected $maxLength = 0;

    /**
     * 当前的队列长度
     * @var int
     */
    protected $currentLength = 0;

    /**
     * 头节点
     * @var Node
     */
    protected $head = null;

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

        if ($this->head == null) {
            $this->head = new Node($val, '');
            $this->currentLength = $this->currentLength + 1;
            return true;
        }

        $current = $this->head;
        $node = new Node($val, '');
        while ($current->next != null) {
            //一直查找到尾节点
            $current = $current->next;
        }

        $current->next = $node;
        $current->next->id = $val;
        $current->next->name = '';

        $this->currentLength = $this->currentLength + 1;
        return true;
    }

    /**
     * 从队列中弹出来
     * @return mixed
     * @throws QueueException
     */
    public function pop()
    {

        if ($this->currentLength - 1 < 0) {
            throw new QueueException("Queue has not any info");
        }

        //删除并返回头节点
        $current = $this->head;
        $popValue = $current->id;
        if ($current->next == null) {
            $this->currentLength = $this->currentLength - 1;
            return $current->id;
        }
        $this->head = $current->next;

        $this->currentLength = $this->currentLength - 1;
        return $popValue;
    }

    /**
     * 获取队列的长度
     * @return mixed
     */
    public function getLength()
    {
        return $this->currentLength;
    }

    public function getMaxLength()
    {

        return $this->maxLength;
    }
}