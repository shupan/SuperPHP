<?php
/**
 * User: phil.shu
 * Date: 2018/1/5
 * Time: 下午8:09
 */

namespace Super\Http;

use ArrayAccess;
use RuntimeException;
use Super\Api\Support\Arrayable;
use Super\Support\Arr;
use Super\Support\Str;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\ParameterBag;


/**
 * HTTP的请求处理
 * 1. HTTP 请求集成了Symfony 框架,支持http的数据信息的整理和数据的处理
 * 2. HTTP的json内容的输入操作
 *
 * Class HttpRequest
 * @package Super\Http
 */
class HttpRequest extends SymfonyRequest implements Arrayable, ArrayAccess
{


    /**
     * 请求解码json数组
     *
     * @var string
     */
    protected $json;

    /**
     * 把request 文件进行转换
     *
     * @var array
     */
    protected $convertedFiles;

    /**
     * 用户回调
     *
     * @var \Closure
     */
    protected $userResolver;

    /**
     * 解析回调
     *
     * @var \Closure
     */
    protected $routeResolver;

    /**
     * 创建Request实例
     *
     * @return static
     */
    public static function capture()
    {
        static::enableHttpMethodParameterOverride();

        return static::createFromBase(SymfonyRequest::createFromGlobals());
    }

    /**
     *
     * 获取Request对象实例
     *
     * @return $this
     */
    public function instance()
    {
        return $this;
    }

    /**
     * 获取request 方法
     *
     * @return string
     */
    public function method()
    {
        return $this->getMethod();
    }

    /**
     * 获取应用的根路径
     *
     * @return string
     */
    public function root()
    {
        return rtrim($this->getSchemeAndHttpHost() . $this->getBaseUrl(), '/');
    }

    /**
     * 请求的endpoint
     *
     * @return string
     */
    public function url()
    {
        return rtrim(preg_replace('/\?.*/', '', $this->getUri()), '/');
    }

    /**
     * 获取请求的整个路径,包括查询的参数信息
     *
     * @return string
     */
    public function fullUrl()
    {
        $query = $this->getQueryString();

        $question = $this->getBaseUrl() . $this->getPathInfo() == '/' ? '/?' : '?';

        return $query ? $this->url() . $question . $query : $this->url();
    }

    /**
     * 对获取的路径,增加查询的参数信息
     *
     * @param  array $query
     * @return string
     */
    public function fullUrlWithQuery(array $query)
    {
        $question = $this->getBaseUrl() . $this->getPathInfo() == '/' ? '/?' : '?';

        return count($this->query()) > 0
            ? $this->url() . $question . http_build_query(array_merge($this->query(), $query))
            : $this->fullUrl() . $question . http_build_query($query);
    }

    /**
     * 获取当前请求的参数信息
     *
     * @return string
     */
    public function path()
    {
        $pattern = trim($this->getPathInfo(), '/');

        return $pattern == '' ? '/' : $pattern;
    }

    /**
     * 对当前的路径进行url解码
     *
     * @return string
     */
    public function decodedPath()
    {
        return rawurldecode($this->path());
    }

    /**
     * 获取url的分段信息
     *
     * @param  int $index
     * @param  string|null $default
     * @return string|null
     */
    public function segment($index, $default = null)
    {
        return Arr::get($this->segments(), $index - 1, $default);
    }

    /**
     * 获取请求的所有分段块
     *
     * @return array
     */
    public function segments()
    {
        $segments = explode('/', $this->decodedPath());

        return array_values(array_filter($segments, function ($v) {
            return $v != '';
        }));
    }

    /**
     * 当前请求是否满足正则表达式
     *
     * @return bool
     */
    public function is()
    {
        foreach (func_get_args() as $pattern) {
            if (Str::is($pattern, $this->decodedPath())) {
                return true;
            }
        }

        return false;
    }

    /**
     * 名称是否满足路由的请
     *
     * @param  string $name
     * @return bool
     */
    public function routeIs($name)
    {
        return $this->route() && $this->route()->named($name);
    }

    /**
     * 当前的请求是否满足正则的路由
     *
     * @return bool
     */
    public function fullUrlIs()
    {
        $url = $this->fullUrl();

        foreach (func_get_args() as $pattern) {
            if (Str::is($pattern, $url)) {
                return true;
            }
        }

        return false;
    }

    /**
     * 判断是否是ajax的请求
     *
     * @return bool
     */
    public function ajax()
    {
        return $this->isXmlHttpRequest();
    }

    /**
     * 判断请求是否支持pjax调用。
     * pjax : 同时支持了缓存和本地存储，下次访问的时候直接读取本地数据，无需在次访问。
     *
     * @return bool
     */
    public function pjax()
    {
        return $this->headers->get('X-PJAX') == true;
    }

    /**
     * 检测是否为https协议
     *
     * @return bool
     */
    public function secure()
    {
        return $this->isSecure();
    }

    /**
     * 获取客户端的ip信息
     *
     * @return string
     */
    public function ip()
    {
        return $this->getClientIp();
    }

    /**
     * 获取客户端的ip列表信息
     *
     * @return array
     */
    public function ips()
    {
        return $this->getClientIps();
    }

    /**
     * 获取客户端的用户代理信息
     *
     * @return string
     */
    public function userAgent()
    {
        return $this->headers->get('User-Agent');
    }

    /**
     * 把当前的输入合并到request中
     *
     * @param  array $input
     * @return void
     */
    public function merge(array $input)
    {
        $this->getInputSource()->add($input);
    }

    /**
     * 提前当前request input信息
     *
     * @param  array $input
     * @return void
     */
    public function replace(array $input)
    {
        $this->getInputSource()->replace($input);
    }

    /**
     * 获取json的信息
     *
     * @param  string $key
     * @param  mixed $default
     * @return mixed
     */
    public function json($key = null, $default = null)
    {
        if (!isset($this->json)) {
            $this->json = new ParameterBag((array)json_decode($this->getContent(), true));
        }

        if (is_null($key)) {
            return $this->json;
        }

        return data_get($this->json->all(), $key, $default);
    }

    /**
     * 获取输入的源信息
     *
     * @return \Symfony\Component\HttpFoundation\ParameterBag
     */
    protected function getInputSource()
    {
        if ($this->isJson()) {
            return $this->json();
        }

        return $this->getRealMethod() == 'GET' ? $this->query : $this->request;
    }

    /**
     * 创建Request 实例
     *
     * @param  \Symfony\Component\HttpFoundation\Request $request
     * @return \Super\Http\SymfonyRequest
     */
    public static function createFromBase(SymfonyRequest $request)
    {
        if ($request instanceof static) {
            return $request;
        }

        $content = $request->content;

        $request = (new static)->duplicate(
            $request->query->all(), $request->request->all(), $request->attributes->all(),
            $request->cookies->all(), $request->files->all(), $request->server->all()
        );

        $request->content = $content;

        $request->request = $request->getInputSource();

        return $request;
    }

    /**
     * {@inheritdoc}
     */
    public function duplicate(array $query = null, array $request = null, array $attributes = null, array $cookies = null, array $files = null, array $server = null)
    {
        return parent::duplicate($query, $request, $attributes, $cookies, $this->filterFiles($files), $server);
    }

    /**
     * 移除文件的空值
     *
     * @param  mixed $files
     * @return mixed
     */
    protected function filterFiles($files)
    {
        if (!$files) {
            return;
        }

        foreach ($files as $key => $file) {
            if (is_array($file)) {
                $files[$key] = $this->filterFiles($files[$key]);
            }

            if (empty($files[$key])) {
                unset($files[$key]);
            }
        }

        return $files;
    }

    /**
     *  获取Request Session
     *
     * @return  mixed
     *
     * @throws \RuntimeException
     */
    public function session()
    {
        if (!$this->hasSession()) {
            throw new RuntimeException('Session store not set on request.');
        }

        return $this->getSession();
    }

    /**
     * 设置当前的session
     *
     * @param  \Super\Api\Session\Session $session
     * @return void
     */
    public function setSession($session)
    {
        $this->session = $session;
    }

    /**
     * 获取用户的请求信息
     *
     * @param  string|null $guard
     * @return mixed
     */
    public function user($guard = null)
    {
        return call_user_func($this->getUserResolver(), $guard);
    }

    /**
     * 获取路由的请求信息
     *
     * @param  string|null $param
     *
     * @return \Super\Router\Route|object|string
     */
    public function route($param = null)
    {
        $route = call_user_func($this->getRouteResolver());

        if (is_null($route) || is_null($param)) {
            return $route;
        }

        return $route->parameter($param);
    }

    /**
     * 根据路由,请求和ip地址获取一个位于地址
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    public function fingerprint()
    {
        if (!$route = $this->route()) {
            throw new RuntimeException('Unable to generate fingerprint. Route unavailable.');
        }

        return sha1(implode('|', array_merge(
            $route->methods(), [$route->domain(), $route->uri(), $this->ip()]
        )));
    }

    /**
     * 设置json数据
     *
     * @param  array $json
     * @return $this
     */
    public function setJson($json)
    {
        $this->json = $json;

        return $this;
    }

    /**
     * 获取用户解析的回调
     *
     * @return \Closure
     */
    public function getUserResolver()
    {
        return $this->userResolver ?: function () {
            //
        };
    }

    /**
     * 设置用户的回调
     *
     * @param  \Closure $callback
     * @return $this
     */
    public function setUserResolver(Closure $callback)
    {
        $this->userResolver = $callback;

        return $this;
    }

    /**
     * Get the route resolver callback.
     *
     * @return \Closure
     */
    public function getRouteResolver()
    {
        return $this->routeResolver ?: function () {
            //
        };
    }

    /**
     * Set the route resolver callback.
     *
     * @param  \Closure $callback
     * @return $this
     */
    public function setRouteResolver(Closure $callback)
    {
        $this->routeResolver = $callback;

        return $this;
    }

    /**
     * 获取所有的key
     *
     * @return array
     */
    public function toArray()
    {
        return $this->all();
    }

    /**
     * 检测可以是否在所有的多存储在
     *
     * @param  string $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->all());
    }

    /**
     * Get the value at the given offset.
     *
     * @param  string $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return data_get($this->all(), $offset);
    }

    /**
     * Set the value at the given offset.
     *
     * @param  string $offset
     * @param  mixed $value
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->getInputSource()->set($offset, $value);
    }

    /**
     * Remove the value at the given offset.
     *
     * @param  string $offset
     * @return void
     */
    public function offsetUnset($offset)
    {
        $this->getInputSource()->remove($offset);
    }

    /**
     * 获取request 是否存在
     *
     * @param  string $key
     * @return bool
     */
    public function __isset($key)
    {
        return !is_null($this->__get($key));
    }

    /**
     * 获取request数据的元素
     *
     * @param  string $key
     * @return mixed
     */
    public function __get($key)
    {
        if ($this->offsetExists($key)) {
            return $this->offsetGet($key);
        }

        return $this->route($key);
    }

}