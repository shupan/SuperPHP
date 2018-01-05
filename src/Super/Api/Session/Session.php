<?php

namespace Super\Api\Session;

interface Session
{
    /**
     * 获取Session 名称
     *
     * @return string
     */
    public function getName();

    /**
     * 获取当前session ID
     *
     * @return string
     */
    public function getId();

    /**
     * 设置session id
     *
     * @param  string $id
     * @return void
     */
    public function setId($id);

    /**
     * 开启session 句柄
     *
     * @return bool
     */
    public function start();

    /**
     * 保存session
     *
     * @return bool
     */
    public function save();

    /**
     * 或所有的session
     *
     * @return array
     */
    public function all();

    /**
     * 检查session是否存在
     *
     * @param  string|array $key
     * @return bool
     */
    public function exists($key);

    /**
     * 检查一个session 不为空
     *
     * @param  string|array $key
     * @return bool
     */
    public function has($key);

    /**
     * 获取一个session值
     *
     * @param  string $key
     * @param  mixed $default
     * @return mixed
     */
    public function get($key, $default = null);

    /**
     * 设置一个session的值
     *
     * @param  string|array $key
     * @param  mixed $value
     * @return void
     */
    public function put($key, $value = null);

    /**
     * 获取csrf token 的值
     *
     * @return string
     */
    public function token();

    /**
     * 移除一个session key
     *
     * @param  string $key
     * @return mixed
     */
    public function remove($key);

    /**
     * 移除多个session 的key
     *
     * @param  string|array $keys
     * @return void
     */
    public function forget($keys);

    /**
     * 移除session的所有项
     *
     * @return void
     */
    public function flush();

    /**
     * 对session 产生一个session id
     *
     * @param  bool $destroy
     * @return bool
     */
    public function migrate($destroy = false);

    /**
     * 检查session 是否已经开启了
     *
     * @return bool
     */
    public function isStarted();


    /**
     * 获取session 的句柄
     *
     * @return \SessionHandlerInterface
     */
    public function getHandler();

    /**
     * 检查该session 是否需要处理
     *
     * @return bool
     */
    public function handlerNeedsRequest();

    /**
     * 设置session需要请求的对象
     *
     * @param  \Super\Http\Request $request
     * @return void
     */
    public function setRequestOnHandler($request);
}
