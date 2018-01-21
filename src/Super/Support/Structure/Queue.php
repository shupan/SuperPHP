<?php
/**
 * User: phil.shu
 * Date: 2018/1/21
 * Time: 下午9:01
 */

namespace Super\Support\Structure;


/**
 * 队列的操作
 * 描述:
 * 1. 支持队列的基本操作,push ,pop , length
 * 2. 队列支持数组的实现,也支持列表的实现方式
 * Class Queue
 * @package Super\Support\Structure
 */
interface Queue
{

    /**
     * 加入队列
     * @param $val
     * @return mixed
     */
    public function push($val);

    /**
     * 从队列中弹出来
     * @return mixed
     */
    public function pop();

    /**
     * 获取队列的长度
     * @return mixed
     */
    public function getLength();
}