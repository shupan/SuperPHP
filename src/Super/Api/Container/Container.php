<?php

namespace Super\Api\Container;

use Closure;

interface Container
{
    /**
     * 确定给定抽象类型是否已绑定
     *
     * @param  string  $abstract
     * @return bool
     */
    public function bound($abstract);

    /**
     * 给抽象类别名
     *
     * @param  string  $abstract
     * @param  string  $alias
     * @return void
     */
    public function alias($abstract, $alias);

    /**
     * 给绑定的事件别名
     *
     * @param  array|string  $abstracts
     * @param  array|mixed   ...$tags
     * @return void
     */
    public function tag($abstracts, $tags);

    /**
     * 给所有绑定的事件给一个tag
     *
     * @param  array  $tag
     * @return array
     */
    public function tagged($tag);

    /**
     * 对容器事件进行绑定
     *
     * @param  string|array  $abstract
     * @param  \Closure|string|null  $concrete
     * @param  bool  $shared
     * @return void
     */
    public function bind($abstract, $concrete = null, $shared = false);

    /**
     * 如果还没有注册,我们就进行一个绑定
     *
     * @param  string  $abstract
     * @param  \Closure|string|null  $concrete
     * @param  bool  $shared
     * @return void
     */
    public function bindIf($abstract, $concrete = null, $shared = false);

    /**
     * 注册一个共享/单例的绑定
     *
     * @param  string|array  $abstract
     * @param  \Closure|string|null  $concrete
     * @return void
     */
    public function singleton($abstract, $concrete = null);

    /**
     * 对容器抽象进行扩展
     *
     * @param  string    $abstract
     * @param  \Closure  $closure
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    public function extend($abstract, Closure $closure);

    /**
     * 注册一个实例容器,可以共享
     *
     * @param  string  $abstract
     * @param  mixed   $instance
     * @return void
     */
    public function instance($abstract, $instance);

    /**
     * 定义绑定的上下文
     *
     * @param  string  $concrete
     * @return \Super\Api\Container\ContextualBindingBuilder
     */
    public function when($concrete);

    /**
     * 获取一个闭包以解析容器中给定的类型。
     *
     * @param  string  $abstract
     * @return \Closure
     */
    public function factory($abstract);

    /**
     * 解析容器的类型
     *
     * @param  string  $abstract
     * @return mixed
     */
    public function make($abstract);

    /**
     * 调用闭包容器的类和方法
     *
     * @param  callable|string  $callback
     * @param  array  $parameters
     * @param  string|null  $defaultMethod
     * @return mixed
     */
    public function call($callback, array $parameters = [], $defaultMethod = null);

    /**
     * 判断闭包的容器类型是否已经解析
     *
     * @param  string $abstract
     * @return bool
     */
    public function resolved($abstract);

    /**
     * 注册一个解析容器的回调
     *
     * @param  string    $abstract
     * @param  \Closure|null  $callback
     * @return void
     */
    public function resolving($abstract, Closure $callback = null);

    /**
     * 在解析回调后,注册一个新的事件
     *
     * @param  string    $abstract
     * @param  \Closure|null  $callback
     * @return void
     */
    public function afterResolving($abstract, Closure $callback = null);
}
