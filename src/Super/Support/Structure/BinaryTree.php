<?php
/**
 * User: phil.shu
 * Date: 2018/1/21
 * Time: 下午9:15
 */

namespace Super\Support\Structure;


/**
 * 构建一棵二叉树
 * 描述:
 * 1. 构建二叉树的根节点
 * 2.
 *
 * Class BinaryTree
 * @package Super\Support\Structure
 */
class BinaryTree implements Tree
{

    protected $arr = [];

    /**
     * 构建二叉树
     * BinaryTree constructor.
     * @param array $arr
     */
    public function __construct(Array $arr)
    {

        $this->arr = $arr;
    }

    /**
     * 初始化二叉树
     */
    protected function initTree()
    {

        foreach ($this->arr as $key => $val) {

            
        }
    }

    /**
     * 增加二叉树节点,
     * @return mixed
     */
    public function create()
    {
        // TODO: Implement create() method.
    }

    /**
     * 根据节点ID获取树的信息
     * @param $id
     * @return mixed
     */
    public function read($id)
    {
        // TODO: Implement read() method.
    }

    /**
     * 更新某棵数的节点情况
     * @param $id
     * @param $val
     * @return mixed
     */
    public function update($id, $val)
    {
        // TODO: Implement update() method.
    }

    /**
     * 删除数的某个节点
     * @param $id
     * @return mixed
     */
    public function delete($id)
    {
        // TODO: Implement delete() method.
    }

    /**
     * 删除这个树的所有节点
     * @return mixed
     */
    public function deleteAll()
    {
        // TODO: Implement deleteAll() method.
    }
}

/**
 * 树节点的构建
 * Class TreeNode
 * @package Super\Support\Structure
 */
class TreeNode
{
    /**
     * 跟节点
     * @var TreeNode
     */
    protected $root = null;

    /**
     * 左节点
     * @var TreeNode
     */
    protected $left = null;

    /**
     * 右节点
     * @var TreeNode
     */
    protected $right = null;

    /**
     * 节点值
     * @var Node
     */
    protected $node = null;

    /**
     * 构建左右节点
     * TreeNode constructor.
     * @param $root
     * @param $left
     * @param $right
     * @param $node
     */
    public function __construct($root, $left = null, $right = null, $node = null)
    {
        if ($node != null) {
            $this->node = $node;
        }
        $this->root = $root;
        if ($this->root->left != null) {
            $this->root->left = $left;
        }

        if ($this->root->right != null) {
            $this->root->right = $right;
        }
    }
}