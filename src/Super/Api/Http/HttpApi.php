<?php

namespace Super\Api\Http;

/**
 * HttpApi 主要是Http的生命周期
 * 描述:
 * 1. 启动
 * 2. 处理
 * 3. 关闭
 * User: phil.shu
 * Date: 2018/1/4
 * Time: 下午9:42
 */
interface HttpApi
{

    /**
     * 启动Http
     * @return mixed
     */
    public function bootstrap();

    /**
     * 处理请求
     * @param \Symfony\Component\HttpFoundation\Request   $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle($request);

    /**
     * 中断处理
     * @param \Symfony\Component\HttpFoundation\Request  $request
     * @param \Symfony\Component\HttpFoundation\Response $response
     * @return mixed
     */
    public function terminate($request, $response);

    /**
     * 获取应用的信息
     * @return \Super\Api\Foundation\Application
     */
    public function getApplication();
}