<?php

namespace Super\Support\Structure;

/**
 * User: phil.shu
 * Date: 2018/1/16
 * Time: 下午11:34
 */
class Node
{

    public $id = '';
    public $name = '';

    /**
     * 节点
     * @var Node
     */
    public $next = null;

    public function __construct($id, $name, $next = null)
    {

        $this->id = $id;
        $this->name = $name;
        $this->next = $next;
    }
}