<?php

namespace Super\Api\Foundation;

use Super\Api\Container\Container;

interface Application extends Container
{
    /**
     * 应用支持的版本
     *
     * @return string
     */
    public function version();

    /**
     * 应用安装的基础路径
     *
     * @return string
     */
    public function basePath();

    /**
     * 获取当前应用的环境配置
     *
     * @return string
     */
    public function environment();

    /**
     * 系统维护开关
     *
     * @return bool
     */
    public function isDownForMaintenance();

    /**
     * 注册所有的需要的配置服务
     *
     * @return void
     */
    public function registerConfiguredProviders();

    /**
     * 应用的服务注册,提供统一的配置服务
     *
     * @param  \Super\Support\ServiceProvider|string  $provider
     * @param  array  $options
     * @param  bool   $force
     * @return \Super\Support\ServiceProvider
     */
    public function register($provider, $options = [], $force = false);

    /**
     * 注册延迟提供程序和服务
     *
     * @param  string  $provider
     * @param  string|null  $service
     * @return void
     */
    public function registerDeferredProvider($provider, $service = null);

    /**
     * 应用的根服务的启动
     *
     * @return void
     */
    public function boot();

    /**
     * 注册一个根服务事件
     *
     * @param  mixed  $callback
     * @return void
     */
    public function booting($callback);

    /**
     * 注册一个 "booted" 新事件
     *
     * @param  mixed  $callback
     * @return void
     */
    public function booted($callback);

    /**
     * 获取缓存服务的路径
     *
     *
     * @return string
     */
    public function getCachedServicesPath();
}
