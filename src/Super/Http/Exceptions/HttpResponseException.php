<?php

namespace Super\Http\Exceptions;

use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

class HttpResponseException extends RuntimeException
{
    /**
     * 请求的Reponse对象
     *
     * @var \Symfony\Component\HttpFoundation\Response
     */
    protected $response;

    /**
     * 获取一个Response实例
     *
     * @param  \Symfony\Component\HttpFoundation\Response  $response
     * @return void
     */
    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    /**
     * 获取Reponse对象
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getResponse()
    {
        return $this->response;
    }
}
