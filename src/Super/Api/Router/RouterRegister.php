<?php
/**
 * User: phil.shu
 * Date: 2018/1/5
 * Time: 下午8:39
 */

namespace Super\Api\Router;


/**
 * RouterRegister
 * Interface RouterRegister
 * @package Super\Api\Router
 */
interface RouterRegister
{

    /**
     * GET方法
     *
     * @param  string  $uri
     * @param  \Closure|array|string  $action
     * @return \Super\Routing\Route
     */
    public function get($uri, $action);

    /**
     *
     * Post 方法
     * @param  string  $uri
     * @param  \Closure|array|string  $action
     * @return \Super\Routing\Route
     */
    public function post($uri, $action);

    /**
     * put 方法
     *
     * @param  string  $uri
     * @param  \Closure|array|string  $action
     * @return \Super\Routing\Route
     */
    public function put($uri, $action);

    /**
     * delete 方法
     *
     * @param  string  $uri
     * @param  \Closure|array|string  $action
     * @return \Super\Routing\Route
     */
    public function delete($uri, $action);

    /**
     * patch 方法
     *
     * @param  string  $uri
     * @param  \Closure|array|string  $action
     * @return \Super\Routing\Route
     */
    public function patch($uri, $action);

    /**
     * options 方法
     *
     * @param  string  $uri
     * @param  \Closure|array|string  $action
     * @return \Super\Routing\Route
     */
    public function options($uri, $action);

    /**
     * match方法
     *
     * @param  array|string  $methods
     * @param  string  $uri
     * @param  \Closure|array|string  $action
     * @return \Super\Routing\Route
     */
    public function match($methods, $uri, $action);

    /**
     * 资源方法
     *
     * @param  string  $name
     * @param  string  $controller
     * @param  array   $options
     * @return void
     */
    public function resource($name, $controller, array $options = []);

    /**
     *  路由组的方法
     *
     * @param  array  $attributes
     * @param  \Closure|string  $routes
     * @return void
     */
    public function group(array $attributes, $routes);
}