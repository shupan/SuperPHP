<?php

namespace Super\Support\Structure;

/**
 * 描述:
 * 链表节点
 * User: phil.shu
 * Date: 2018/1/16
 * Time: 下午11:34
 */
class LinkedTable
{

    /**
     * 头结点
     * @var Node
     */
    protected $head = null;


    /**
     * 初始化头部节点
     * LinkedTable constructor.
     * @param $id
     * @param $name
     */
    public function __construct($id, $name)
    {
        $this->head = new Node($id, $name, null);
    }

    /**
     * 添加节点
     * @param Node $node
     * @return  bool
     */
    public function addNode(Node $node)
    {

        if (empty($this->head)) {
            $this->head = $node;
            return true;
        }
        $current = $this->head;
        while ($current->next != null) {

            //后面加的节点的ID值一定是会比当前的节点大的
            if ($current->next->id > $node->id) {
                break;
            }
            $current = $current->next;
        }

        //把要加入的节点作为当前节点的下一个节点
        $current->next = $node;

        return true;

    }

    /**
     * 根据ID值来删除节点
     * @param $id
     * @return  bool
     */
    public function deleteNode($id)
    {
        $current = $this->head;
        while ($current->next != null) {
            if ($current->next->id == $id) {

                //把后面的节点替换当前的节点
                $current->next = $current->next->next;
                return true;
            }
            $current = $current->next;
        }

        //删除头节点的情况
        if ($current->id == $id) {
            $this->head = null;
            return true;
        }
        return false;

    }

    /**
     * 根据节点获取用户的信息
     * @param $id
     * @return bool id
     */
    public function getNode($id)
    {
        $current = $this->head;
        if ($current == null) {
            return false;
        }

        if ($current->id == $id) {
            return $current->name;
        }

        while ($current->next != null) {
            if ($current->next->id == $id) {

                return $current->next->name;
            }
            $current = $current->next;
        }

        return false;

    }


    /**
     * 打印这个链表所有的节点的数据
     */
    public function printNode()
    {

        $current = $this->head;
        while ($current != null) {
            //输出当前节点的的值
            echo "id:" . $current->id . ",name:" . $current->name . "\n";
            $current = $current->next;
        }
    }


    /**
     * 获取链表的长度
     */
    public function getLength()
    {

        $cnt = 0;
        $current = $this->head;
        if (!empty($current)) {
            $cnt = $cnt + 1;
        } else {
            return $cnt;
        }
        while ($current->next != null) {

            $cnt = $cnt + 1;
            $current = $current->next;
        }
        return $cnt;
    }

}