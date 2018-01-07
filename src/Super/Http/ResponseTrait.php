<?php

namespace Super\Http;

use Exception;
use Super\Http\Exceptions\HttpResponseException;

trait ResponseTrait
{
    /**
     * 返回的原始内容
     *
     * @var mixed
     */
    public $original;

    /**
     * 应用的异常信息
     *
     * @var \Exception|null
     */
    public $exception;

    /**
     * 返回的状态码
     *
     * @return int
     */
    public function status()
    {
        return $this->getStatusCode();
    }

    /**
     * 获取返回的内容信息
     *
     * @return string
     */
    public function content()
    {
        return $this->getContent();
    }

    /**
     * 获取原先的response 信息
     *
     * @return mixed
     */
    public function getOriginalContent()
    {
        return $this->original;
    }

    /**
     * 设置请求头的信息
     *
     * @param  string  $key
     * @param  array|string  $values
     * @param  bool    $replace
     * @return $this
     */
    public function header($key, $values, $replace = true)
    {
        $this->headers->set($key, $values, $replace);

        return $this;
    }

    /**
     * Add an array of headers to the response.
     *
     * @param  array  $headers
     * @return $this
     */
    public function withHeaders(array $headers)
    {
        foreach ($headers as $key => $value) {
            $this->headers->set($key, $value);
        }

        return $this;
    }

    /**
     * Add a cookie to the response.
     *
     * @param  \Symfony\Component\HttpFoundation\Cookie|mixed  $cookie
     * @return $this
     */
    public function cookie($cookie)
    {
        return call_user_func_array([$this, 'withCookie'], func_get_args());
    }

    /**
     * Add a cookie to the response.
     *
     * @param  \Symfony\Component\HttpFoundation\Cookie|mixed  $cookie
     * @return $this
     */
    public function withCookie($cookie)
    {
        if (is_string($cookie) && function_exists('cookie')) {
            $cookie = call_user_func_array('cookie', func_get_args());
        }

        $this->headers->setCookie($cookie);

        return $this;
    }

    /**
     * Set the exception to attach to the response.
     *
     * @param  \Exception  $e
     * @return $this
     */
    public function withException(Exception $e)
    {
        $this->exception = $e;

        return $this;
    }

    /**
     * Throws the response in a HttpResponseException instance.
     *
     * @throws \Super\Http\Exceptions\HttpResponseException
     */
    public function throwResponse()
    {
        throw new HttpResponseException($this);
    }
}
