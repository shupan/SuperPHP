<?php
/**
 * User: phil.shu
 * Date: 2018/1/5
 * Time: 下午8:52
 */

namespace Super\Api\Router;


interface BingingRegister
{

    /**
     * 增加一个绑定的路由参数
     *
     * @param  string  $key
     * @param  string|callable  $binder
     * @return void
     */
    public function bind($key, $binder);

    /**
     * 获取绑定的路由回调
     *
     * @param  string  $key
     * @return \Closure
     */
    public function getBindingCallback($key);
}