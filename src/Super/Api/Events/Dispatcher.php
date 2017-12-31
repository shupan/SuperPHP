<?php

namespace Super\Api\Events;

interface Dispatcher
{
    /**
     * 监听调用的事件
     *
     * @param  string|array  $events
     * @param  mixed  $listener
     * @return void
     */
    public function listen($events, $listener);

    /**
     * 检查事件是否存在监听
     *
     * @param  string  $eventName
     * @return bool
     */
    public function hasListeners($eventName);

    /**
     * 订阅一个监听的事件
     *
     * @param  object|string  $subscriber
     * @return void
     */
    public function subscribe($subscriber);

    /**
     * 处理一个监听的事件,直到没有任何数据的返回
     *
     * @param  string|object  $event
     * @param  mixed  $payload
     * @return array|null
     */
    public function until($event, $payload = []);

    /**
     * 调用事件的监听
     *
     * @param  string|object  $event
     * @param  mixed  $payload
     * @param  bool  $halt
     * @return array|null
     */
    public function dispatch($event, $payload = [], $halt = false);

    /**
     * 注册事件后,把数据信息进行推送
     *
     * @param  string  $event
     * @param  array  $payload
     * @return void
     */
    public function push($event, $payload = []);

    /**
     * 清空推荐的事件列表
     *
     * @param  string  $event
     * @return void
     */
    public function flush($event);

    /**
     * 删除一系列的监控事件
     *
     * @param  string  $event
     * @return void
     */
    public function forget($event);

    /**
     * 清空队列的监控事件
     *
     * @return void
     */
    public function forgetPushed();
}
