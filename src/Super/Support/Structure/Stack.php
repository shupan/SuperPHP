<?php
/**
 * User: phil.shu
 * Date: 2018/1/21
 * Time: 下午9:08
 */

namespace Super\Support\Structure;


class Stack
{

    /**
     * 栈的大小
     * @var array
     */
    protected $arr = [];

    /**
     * 栈最大长度
     * @var int
     */
    protected $maxLength = 0;

    /**
     * 当前的长度
     * @var int
     */
    protected $currentLength = 0;

    /**
     * 设置栈的大小
     * Stack constructor.
     * @param $maxLength
     */
    public function __construct($maxLength)
    {
        $this->maxLength = $maxLength;
    }

    /**
     * push 到栈里面
     * @param $val
     * @return  boolean
     */
    public function push($val)
    {
        if ($this->getLength() >= $this->getMaxLength()) {
            return false;
        }

        $this->arr[] = $val;
        $this->currentLength = $this->currentLength + 1;
        return true;
    }

    /**
     * 弹出栈的值
     */
    public function pop()
    {

        if ($this->currentLength - 1 < 0) {
            return false;
        }

        $this->currentLength = $this->currentLength - 1;
        return array_pop($this->arr);

    }

    public function getMaxLength()
    {
        return $this->maxLength;
    }

    /**
     * 获取栈的长度
     */
    public function getLength()
    {

        return $this->currentLength;
    }
}