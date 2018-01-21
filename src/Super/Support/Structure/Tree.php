<?php
/**
 * User: phil.shu
 * Date: 2018/1/21
 * Time: 下午9:10
 */

namespace Super\Support\Structure;


interface Tree
{

    /**
     * 创建数
     * @return mixed
     */
    public function create();

    /**
     * 根据节点ID获取树的信息
     * @param $id
     * @return mixed
     */
    public function read($id);

    /**
     * 更新某棵数的节点情况
     * @param $id
     * @param $val
     * @return mixed
     */
    public function update($id, $val);

    /**
     * 删除数的某个节点
     * @param $id
     * @return mixed
     */
    public function delete($id);

    /**
     * 删除这个树的所有节点
     * @return mixed
     */
    public function deleteAll();
}