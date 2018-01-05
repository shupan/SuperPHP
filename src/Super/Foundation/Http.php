<?php
/**
 * User: phil.shu
 * Date: 2018/1/5
 * Time: 下午8:33
 */

namespace Super\Foundation\Http;


use Super\Api\Foundation\Application;
use Super\Api\Http\HttpApi;
use Super\Router\Router;

class Http implements HttpApi
{

    /**
     * 应用对象
     * @var \Super\Api\Foundation\Application
     */
    protected $app;

    /**
     * 路由的对象
     * @var \Super\Router\Router
     */
    protected $router;

    /**
     * Create a new HTTP kernel instance.
     *
     * @param  \Super\Api\Foundation\Application $app
     * @param  \Super\Router\Router $router
     */
    public function __construct(Application $app, Router $router)
    {

        $this->app = $app;
        $this->router = $router;
    }

    /**
     * 启动Http
     * @return mixed
     */
    public function bootstrap()
    {
        // TODO: Implement bootstrap() method.
    }

    /**
     * 处理请求
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle($request)
    {
        // TODO: Implement handle() method.
    }

    /**
     * 中断处理
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Symfony\Component\HttpFoundation\Response $response
     * @return mixed
     */
    public function terminate($request, $response)
    {
        // TODO: Implement terminate() method.
    }

    /**
     * 获取应用的信息
     * @return \Super\Api\Foundation\Application
     */
    public function getApplication()
    {
        // TODO: Implement getApplication() method.
    }
}