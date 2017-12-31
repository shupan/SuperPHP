<?php

namespace Super\Foundation;

use Closure;
use RuntimeException;
use Super\Filesystem\LocalFilesystem;
use Super\Support\Arr;
use Super\Support\Str;
use Super\Container\Container;
use Super\Filesystem\Filesystem;
use Super\Support\ServiceProvider;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Super\Api\Foundation\Application as ApplicationContract;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;


/**
 * 描述:
 *     Application 是这个SuperPHP框架的引擎,它把所有的组件和工具抽象成服务,
 * 然后通过注册/绑定的机制进行使用。
 *
 * Class Application
 * @package Super\Foundation
 */
class Application extends Container implements ApplicationContract, HttpKernelInterface
{
    /**
     * Super 框架的版本
     *
     * @var string
     */
    const VERSION = '1.0.0';

    /**
     *  安装的基本路径信息
     *
     * @var string
     */
    protected $basePath;

    /**
     * 是否已经做好了启动的准备
     *
     * @var bool
     */
    protected $hasBeenBootstrapped = false;

    /**
     * 应用是否已经启动了
     *
     * @var bool
     */
    protected $booted = false;

    /**
     * 启动过程的数组回调
     *
     * @var array
     */
    protected $bootingCallbacks = [];

    /**
     * 启动后的数组回调
     *
     * @var array
     */
    protected $bootedCallbacks = [];

    /**
     * 数组中断的事件回调
     *
     * @var array
     */
    protected $terminatingCallbacks = [];

    /**
     * 所有注册服务的提供
     *
     * @var array
     */
    protected $serviceProviders = [];

    /**
     * 服务加载
     *
     * @var array
     */
    protected $loadedProviders = [];

    /**
     * 延迟服务及其提供者
     *
     * @var array
     */
    protected $deferredServices = [];

    /**
     * 对mongo 配置的回调
     *
     * @var callable|null
     */
    protected $monologConfigurator;

    /**
     * 数据库配置路径
     *
     * @var string
     */
    protected $databasePath;

    /**
     * 存储路径
     *
     * @var string
     */
    protected $storagePath;

    /**
     * 环境路径
     *
     * @var string
     */
    protected $environmentPath;

    /**
     * 环境启动的.env文件的配置
     *
     * @var string
     */
    protected $environmentFile = '.env';

    /**
     * 应用的命名空间
     *
     * @var string
     */
    protected $namespace;

    /**
     * 创建应用实例的主进程,然后创建相应的子进程服务
     *
     * @param  string|null  $basePath
     * @return void
     */
    public function __construct($basePath = null)
    {
        if ($basePath) {
            $this->setBasePath($basePath);
        }

        $this->registerBaseBindings();

        $this->registerBaseServiceProviders();

        $this->registerCoreContainerAliases();
    }

    /**
     * 获取应用的版本
     *
     * @return string
     */
    public function version()
    {
        return static::VERSION;
    }

    /**
     * 容器注册事件的绑定
     *
     * @return void
     */
    protected function registerBaseBindings()
    {
        static::setInstance($this);

        $this->instance('app', $this);

        $this->instance(Container::class, $this);
    }

    /**
     * 注册所有的基本服务,如事件,日志和路由
     *
     * @return void
     */
    protected function registerBaseServiceProviders()
    {
//        $this->register(new EventServiceProvider($this));
//
//        $this->register(new LogServiceProvider($this));
//
//        $this->register(new RoutingServiceProvider($this));
    }

    /**
     * 启动的事件准备
     *
     * @param  array  $bootstrappers
     * @return void
     */
    public function bootstrapWith(array $bootstrappers)
    {
        $this->hasBeenBootstrapped = true;

//        foreach ($bootstrappers as $bootstrapper) {
//            $this['events']->fire('bootstrapping: '.$bootstrapper, [$this]);
//
//            $this->make($bootstrapper)->bootstrap($this);
//
//            $this['events']->fire('bootstrapped: '.$bootstrapper, [$this]);
//        }
    }

    /**
     * 注册绑定后加载系统的环境变量
     *
     * @param  \Closure  $callback
     * @return void
     */
    public function afterLoadingEnvironment(Closure $callback)
    {
//        return $this->afterBootstrapping(
//            LoadEnvironmentVariables::class, $callback
//        );
        return '';
    }

    /**
     * 在启动前注册事件的回调(AOP)
     *
     * @param  string  $bootstrapper
     * @param  Closure  $callback
     * @return void
     */
    public function beforeBootstrapping($bootstrapper, Closure $callback)
    {
        $this['events']->listen('bootstrapping: '.$bootstrapper, $callback);
    }

    /**
     * 在启动之后注册时间的回调(AOP)
     *
     * @param  string  $bootstrapper
     * @param  Closure  $callback
     * @return void
     */
    public function afterBootstrapping($bootstrapper, Closure $callback)
    {
        $this['events']->listen('bootstrapped: '.$bootstrapper, $callback);
    }

    /**
     * 判断是否已经做好了启动的准备
     *
     * @return bool
     */
    public function hasBeenBootstrapped()
    {
        return $this->hasBeenBootstrapped;
    }

    /**
     * 设置应用的path
     *
     * @param  string  $basePath
     * @return $this
     */
    public function setBasePath($basePath)
    {
        $this->basePath = rtrim($basePath, '\/');

        $this->bindPathsInContainer();

        return $this;
    }

    /**
     * 绑定应用的所有的配置路径进行
     *
     * @return void
     */
    protected function bindPathsInContainer()
    {
        $this->instance('path', $this->path());
        $this->instance('path.base', $this->basePath());
        $this->instance('path.lang', $this->langPath());
        $this->instance('path.config', $this->configPath());
        $this->instance('path.public', $this->publicPath());
        $this->instance('path.storage', $this->storagePath());
        $this->instance('path.database', $this->databasePath());
        $this->instance('path.resources', $this->resourcePath());
        $this->instance('path.bootstrap', $this->bootstrapPath());
    }

    /**
     * 应用"app" 目录
     *
     * @param string $path Optionally, a path to append to the app path
     * @return string
     */
    public function path($path = '')
    {
        return $this->basePath.DIRECTORY_SEPARATOR.'app'.($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

    /**
     * 基础的路径
     *
     * @param string $path Optionally, a path to append to the base path
     * @return string
     */
    public function basePath($path = '')
    {
        return $this->basePath.($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

    /**
     * 启动的根目录路径
     *
     * @param string $path Optionally, a path to append to the bootstrap path
     * @return string
     */
    public function bootstrapPath($path = '')
    {
        return $this->basePath.DIRECTORY_SEPARATOR.'bootstrap'.($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

    /**
     * 应用的环境配置路径
     *
     * @param string $path Optionally, a path to append to the config path
     * @return string
     */
    public function configPath($path = '')
    {
        return $this->basePath.DIRECTORY_SEPARATOR.'config'.($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

    /**
     * 数据库配置目录
     *
     * @param string $path Optionally, a path to append to the database path
     * @return string
     */
    public function databasePath($path = '')
    {
        return ($this->databasePath ?: $this->basePath.DIRECTORY_SEPARATOR.'database').($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

    /**
     * 设置数据库配置目录
     *
     * @param  string  $path
     * @return $this
     */
    public function useDatabasePath($path)
    {
        $this->databasePath = $path;

        $this->instance('path.database', $path);

        return $this;
    }

    /**
     * 获取语言包的路径
     *
     * @return string
     */
    public function langPath()
    {
        return $this->resourcePath().DIRECTORY_SEPARATOR.'lang';
    }

    /**
     * 获取Web发布的路径信息
     *
     * @return string
     */
    public function publicPath()
    {
        return $this->basePath.DIRECTORY_SEPARATOR.'public';
    }

    /**
     * 获取项目的存储路径
     *
     * @return string
     */
    public function storagePath()
    {
        return $this->storagePath ?: $this->basePath.DIRECTORY_SEPARATOR.'storage';
    }

    /**
     * 设置存储的目录
     *
     * @param  string  $path
     * @return $this
     */
    public function useStoragePath($path)
    {
        $this->storagePath = $path;

        $this->instance('path.storage', $path);

        return $this;
    }

    /**
     * 获取资源路径
     *
     * @param  string  $path
     * @return string
     */
    public function resourcePath($path = '')
    {
        return $this->basePath.DIRECTORY_SEPARATOR.'resources'.($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

    /**
     * 获取环境变量的路径信息
     *
     * @return string
     */
    public function environmentPath()
    {
        return $this->environmentPath ?: $this->basePath;
    }

    /**
     * 使用环境变量的路径
     *
     * @param  string  $path
     * @return $this
     */
    public function useEnvironmentPath($path)
    {
        $this->environmentPath = $path;

        return $this;
    }

    /**
     * 加载环境变量
     *
     * @param  string  $file
     * @return $this
     */
    public function loadEnvironmentFrom($file)
    {
        $this->environmentFile = $file;

        return $this;
    }

    /**
     * 读取环境变量的文件
     *
     * @return string
     */
    public function environmentFile()
    {
        return $this->environmentFile ?: '.env';
    }

    /**
     * 读取所有的环境变量的文件
     *
     * @return string
     */
    public function environmentFilePath()
    {
        return $this->environmentPath().'/'.$this->environmentFile();
    }

    /**
     * 检查当前应用的环境
     *
     * @return string|bool
     */
    public function environment()
    {
        if (func_num_args() > 0) {
            $patterns = is_array(func_get_arg(0)) ? func_get_arg(0) : func_get_args();

            foreach ($patterns as $pattern) {
                if (Str::is($pattern, $this['env'])) {
                    return true;
                }
            }

            return false;
        }

        return $this['env'];
    }

    /**
     * 判断应用环境的变量,如开发环境,测试环境,线上环境等
     *
     * @return bool
     */
    public function isLocal()
    {
        return $this['env'] == 'local';
    }

    /**
     * 检查系统当前的应用环境
     *
     * @param  \Closure  $callback
     * @return string
     */
    public function detectEnvironment(Closure $callback)
    {
        $args = isset($_SERVER['argv']) ? $_SERVER['argv'] : null;

        return $this['env'] = (new EnvironmentDetector())->detect($callback, $args);
    }

    /**
     * 判断是否为CLI模式 Command-Line
     *
     * @return bool
     */
    public function runningInConsole()
    {
        return php_sapi_name() == 'cli' || php_sapi_name() == 'phpdbg';
    }

    /**
     * 检查是否可以运行单元的测试
     *
     * @return bool
     */
    public function runningUnitTests()
    {
        return $this['env'] == 'testing';
    }

    /**
     * 注册所有的配置服务
     *
     * @return void
     */
    public function registerConfiguredProviders()
    {
//        (new ProviderRepository($this, new LocalFilesystem(), $this->getCachedServicesPath()))
//                    ->load($this->config['app.providers']);
    }

    /**
     * 注册应用的服务事件
     *
     * @param  \Super\Support\ServiceProvider|string  $provider
     * @param  array  $options
     * @param  bool   $force
     * @return \Super\Support\ServiceProvider
     */
    public function register($provider, $options = [], $force = false)
    {
        if (($registered = $this->getProvider($provider)) && ! $force) {
            return $registered;
        }

        // If the given "provider" is a string, we will resolve it, passing in the
        // application instance automatically for the developer. This is simply
        // a more convenient way of specifying your service provider classes.
        if (is_string($provider)) {
            $provider = $this->resolveProvider($provider);
        }

        if (method_exists($provider, 'register')) {
            $provider->register();
        }

        $this->markAsRegistered($provider);

        // If the application has already booted, we will call this boot method on
        // the provider class so it has an opportunity to do its boot logic and
        // will be ready for any usage by this developer's application logic.
        if ($this->booted) {
            $this->bootProvider($provider);
        }

        return $provider;
    }

    /**
     * 获取注册的服务实例,如果它存在
     *
     * @param  \Super\Support\ServiceProvider|string  $provider
     * @return \Super\Support\ServiceProvider|null
     */
    public function getProvider($provider)
    {
        $name = is_string($provider) ? $provider : get_class($provider);

        return Arr::first($this->serviceProviders, function ($value) use ($name) {
            return $value instanceof $name;
        });
    }

    /**
     * 从类名解析器服务
     *
     * @param  string  $provider
     * @return \Super\Support\ServiceProvider
     */
    public function resolveProvider($provider)
    {
        return new $provider($this);
    }

    /**
     * 标识已经提供的服务
     *
     * @param  \Super\Support\ServiceProvider  $provider
     * @return void
     */
    protected function markAsRegistered($provider)
    {
        $this->serviceProviders[] = $provider;

        $this->loadedProviders[get_class($provider)] = true;
    }

    /**
     * 加载并引导所有剩余的延迟服务
     * 延迟启动可以减少应用启动的速度过慢的情况
     * @return void
     */
    public function loadDeferredProviders()
    {
        foreach ($this->deferredServices as $service => $provider) {
            $this->loadDeferredProvider($service);
        }

        $this->deferredServices = [];
    }

    /**
     * 加载一个延迟的服务
     *
     * @param  string  $service
     * @return void
     */
    public function loadDeferredProvider($service)
    {
        if (! isset($this->deferredServices[$service])) {
            return;
        }

        $provider = $this->deferredServices[$service];

        //如果注册的服务已经加载了就不会重新的加载了
        if (! isset($this->loadedProviders[$provider])) {
            $this->registerDeferredProvider($provider, $service);
        }
    }

    /**
     * 注册和提供一个延迟的服务
     *
     * @param  string  $provider
     * @param  string|null  $service
     * @return void
     */
    public function registerDeferredProvider($provider, $service = null)
    {
        if ($service) {
            unset($this->deferredServices[$service]);
        }

        $this->register($instance = new $provider($this));

        if (! $this->booted) {
            $this->booting(function () use ($instance) {
                $this->bootProvider($instance);
            });
        }
    }

    /**
     * 从容器中解析给定类型。
     *
     * @param  string  $abstract
     * @param  array  $parameters
     * @return mixed
     */
    public function makeWith($abstract, array $parameters)
    {
//        $abstract = $this->getAlias($abstract);
//
//        if (isset($this->deferredServices[$abstract])) {
//            $this->loadDeferredProvider($abstract);
//        }
//
//        return parent::makeWith($abstract, $parameters);
        return '';
    }

    /**
     * 容器的类型解析
     *
     * (重写 Container::make)
     *
     * @param  string  $abstract
     * @return mixed
     */
    public function make($abstract)
    {
        $abstract = $this->getAlias($abstract);

        if (isset($this->deferredServices[$abstract])) {
            $this->loadDeferredProvider($abstract);
        }

        return parent::make($abstract);
    }

    /**
     * 确定给定抽象类型是否已绑定。
     *
     * (Overriding Container::bound)
     *
     * @param  string  $abstract
     * @return bool
     */
    public function bound($abstract)
    {
        return isset($this->deferredServices[$abstract]) || parent::bound($abstract);
    }

    /**
     * 判断容器是否已经启动了
     *
     * @return bool
     */
    public function isBooted()
    {
        return $this->booted;
    }

    /**
     * 应用服务是否已经注册了
     *
     * @return void
     */
    public function boot()
    {
        if ($this->booted) {
            return;
        }

        //一旦服务启动了会调用事件的监听操作
        $this->fireAppCallbacks($this->bootingCallbacks);

        array_walk($this->serviceProviders, function ($p) {
            $this->bootProvider($p);
        });

        $this->booted = true;

        $this->fireAppCallbacks($this->bootedCallbacks);
    }

    /**
     * 启动提供的服务
     *
     * @param  \Super\Support\ServiceProvider  $provider
     * @return mixed
     */
    protected function bootProvider(ServiceProvider $provider)
    {
        if (method_exists($provider, 'boot')) {
            return $this->call([$provider, 'boot']);
        }
    }

    /**
     * 注册启动的监听事件
     *
     * @param  mixed  $callback
     * @return void
     */
    public function booting($callback)
    {
        $this->bootingCallbacks[] = $callback;
    }

    /**
     * 注册一个已经"booted"事件
     *
     * @param  mixed  $callback
     * @return void
     */
    public function booted($callback)
    {
        $this->bootedCallbacks[] = $callback;

        if ($this->isBooted()) {
            $this->fireAppCallbacks([$callback]);
        }
    }

    /**
     * 应用启动事件的回调
     *
     * @param  array  $callbacks
     * @return void
     */
    protected function fireAppCallbacks(array $callbacks)
    {
        foreach ($callbacks as $callback) {
            call_user_func($callback, $this);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function handle(SymfonyRequest $request, $type = self::MASTER_REQUEST, $catch = true)
    {
        //return $this[HttpKernelContract::class]->handle(Request::createFromBase($request));
        return '';
    }

    /**
     * 应用的中间件层忽略
     *
     * @return bool
     */
    public function shouldSkipMiddleware()
    {
        return $this->bound('middleware.disable') &&
               $this->make('middleware.disable') === true;
    }

    /**
     * 获取services的缓存
     *
     * @return string
     */
    public function getCachedServicesPath()
    {
        return $this->bootstrapPath().'/cache/services.php';
    }

    /**
     * 决定应用的配置是否环境
     *
     * @return bool
     */
    public function configurationIsCached()
    {
        return file_exists($this->getCachedConfigPath());
    }

    /**
     * 获取环境的配置
     *
     * @return string
     */
    public function getCachedConfigPath()
    {
        return $this->bootstrapPath().'/cache/config.php';
    }

    /**
     * 判断路由是否已经缓存
     *
     * @return bool
     */
    public function routesAreCached()
    {
        return $this['files']->exists($this->getCachedRoutesPath());
    }

    /**
     * 获取路由缓存的配置
     *
     * @return string
     */
    public function getCachedRoutesPath()
    {
        return $this->bootstrapPath().'/cache/routes.php';
    }

    /**
     * 检查系统的环境是否在维护状态
     *
     * @return bool
     */
    public function isDownForMaintenance()
    {
        return file_exists($this->storagePath().'/framework/down');
    }

    /**
     * 对于系统中断服务扔出404异常信息
     *
     * @param  int     $code
     * @param  string  $message
     * @param  array   $headers
     * @return void
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function abort($code, $message = '', array $headers = [])
    {
        if ($code == 404) {
            throw new NotFoundHttpException($message);
        }

        throw new HttpException($code, $message, null, $headers);
    }

    /**
     * 注册一个中断的回调事件
     *
     * @param  \Closure  $callback
     * @return $this
     */
    public function terminating(Closure $callback)
    {
        $this->terminatingCallbacks[] = $callback;

        return $this;
    }

    /**
     * 中断应用操作
     *
     * @return void
     */
    public function terminate()
    {
        foreach ($this->terminatingCallbacks as $terminating) {
            $this->call($terminating);
        }
    }

    /**
     * 获取应用的服务已经加载
     *
     * @return array
     */
    public function getLoadedProviders()
    {
        return $this->loadedProviders;
    }

    /**
     * 获取应用的延迟服务
     *
     * @return array
     */
    public function getDeferredServices()
    {
        return $this->deferredServices;
    }

    /**
     * 配置应用的延迟服务
     *
     * @param  array  $services
     * @return void
     */
    public function setDeferredServices(array $services)
    {
        $this->deferredServices = $services;
    }

    /**
     * 增加服务的延迟加载服务
     *
     * @param  array  $services
     * @return void
     */
    public function addDeferredServices(array $services)
    {
        $this->deferredServices = array_merge($this->deferredServices, $services);
    }

    /**
     * 是否延迟的服务
     *
     * @param  string  $service
     * @return bool
     */
    public function isDeferredService($service)
    {
        return isset($this->deferredServices[$service]);
    }

    /**
     * 配置facade提供服务
     *
     * @param  string  $namespace
     * @return void
     */
    public function provideFacades($namespace)
    {
        AliasLoader::setFacadeNamespace($namespace);
    }

    /**
     * Monolog 日志的回调
     *
     * @param  callable  $callback
     * @return $this
     */
    public function configureMonologUsing(callable $callback)
    {
        $this->monologConfigurator = $callback;

        return $this;
    }

    /**
     * 判断应用是否有Monolog配置
     *
     * @return bool
     */
    public function hasMonologConfigurator()
    {
        return ! is_null($this->monologConfigurator);
    }

    /**
     * 获取对Monolog的配置器
     *
     * @return callable
     */
    public function getMonologConfigurator()
    {
        return $this->monologConfigurator;
    }

    /**
     * 获取App的本地语言库
     *
     * @return string
     */
    public function getLocale()
    {
        return $this['config']->get('app.locale');
    }

    /**
     * 设置当前应用的语言
     *
     * @param  string  $locale
     * @return void
     */
    public function setLocale($locale)
    {
        $this['config']->set('app.locale', $locale);

        $this['translator']->setLocale($locale);

       // $this['events']->dispatch(new Events\LocaleUpdated($locale));
    }

    /**
     * Determine if application locale is the given locale.
     *
     * @param  string  $locale
     * @return bool
     */
    public function isLocale($locale)
    {
        return $this->getLocale() == $locale;
    }

    /**
     * 注册核心类到容器里面
     *
     * @return void
     */
    public function registerCoreContainerAliases()
    {
        foreach ([
//            'app'                  => [\Super\Foundation\Application::class, \Super\Contracts\Container\Container::class, \Super\Contracts\Foundation\Application::class],
//            'auth'                 => [\Super\Auth\AuthManager::class, \Super\Contracts\Auth\Factory::class],
//            'auth.driver'          => [\Super\Contracts\Auth\Guard::class],
//            'blade.compiler'       => [\Super\View\Compilers\BladeCompiler::class],
//            'cache'                => [\Super\Cache\CacheManager::class, \Super\Contracts\Cache\Factory::class],
//            'cache.store'          => [\Super\Cache\Repository::class, \Super\Contracts\Cache\Repository::class],
//            'config'               => [\Super\Config\Repository::class, \Super\Contracts\Config\Repository::class],
//            'cookie'               => [\Super\Cookie\CookieJar::class, \Super\Contracts\Cookie\Factory::class, \Super\Contracts\Cookie\QueueingFactory::class],
//            'encrypter'            => [\Super\Encryption\Encrypter::class, \Super\Contracts\Encryption\Encrypter::class],
//            'db'                   => [\Super\Database\DatabaseManager::class],
//            'db.connection'        => [\Super\Database\Connection::class, \Super\Database\ConnectionInterface::class],
//            'events'               => [\Super\Events\Dispatcher::class, \Super\Contracts\Events\Dispatcher::class],
//            'files'                => [\Super\Filesystem\Filesystem::class],
//            'filesystem'           => [\Super\Filesystem\FilesystemManager::class, \Super\Contracts\Filesystem\Factory::class],
//            'filesystem.disk'      => [\Super\Contracts\Filesystem\Filesystem::class],
//            'filesystem.cloud'     => [\Super\Contracts\Filesystem\Cloud::class],
//            'hash'                 => [\Super\Contracts\Hashing\Hasher::class],
//            'translator'           => [\Super\Translation\Translator::class, \Super\Contracts\Translation\Translator::class],
//            'log'                  => [\Super\Log\Writer::class, \Super\Contracts\Logging\Log::class, \Psr\Log\LoggerInterface::class],
//            'mailer'               => [\Super\Mail\Mailer::class, \Super\Contracts\Mail\Mailer::class, \Super\Contracts\Mail\MailQueue::class],
//            'auth.password'        => [\Super\Auth\Passwords\PasswordBrokerManager::class, \Super\Contracts\Auth\PasswordBrokerFactory::class],
//            'auth.password.broker' => [\Super\Auth\Passwords\PasswordBroker::class, \Super\Contracts\Auth\PasswordBroker::class],
//            'queue'                => [\Super\Queue\QueueManager::class, \Super\Contracts\Queue\Factory::class, \Super\Contracts\Queue\Monitor::class],
//            'queue.connection'     => [\Super\Contracts\Queue\Queue::class],
//            'queue.failer'         => [\Super\Queue\Failed\FailedJobProviderInterface::class],
//            'redirect'             => [\Super\Routing\Redirector::class],
//            'redis'                => [\Super\Redis\RedisManager::class, \Super\Contracts\Redis\Factory::class],
//            'request'              => [\Super\Http\Request::class, \Symfony\Component\HttpFoundation\Request::class],
//            'router'               => [\Super\Routing\Router::class, \Super\Contracts\Routing\Registrar::class, \Super\Contracts\Routing\BindingRegistrar::class],
//            'session'              => [\Super\Session\SessionManager::class],
//            'session.store'        => [\Super\Session\Store::class, \Super\Contracts\Session\Session::class],
//            'url'                  => [\Super\Routing\UrlGenerator::class, \Super\Contracts\Routing\UrlGenerator::class],
//            'validator'            => [\Super\Validation\Factory::class, \Super\Contracts\Validation\Factory::class],
//            'view'                 => [\Super\View\Factory::class, \Super\Contracts\View\Factory::class],
        ] as $key => $aliases) {
            foreach ($aliases as $alias) {
                $this->alias($key, $alias);
            }
        }
    }

    /**
     * 清空所有的信息绑定
     *
     * @return void
     */
    public function flush()
    {
        parent::flush();

        $this->loadedProviders = [];
        $this->bootingCallbacks = [];
        $this->bootedCallbacks = [];
        $this->middlewares = [];
        $this->serviceProviders = [];
        $this->deferredServices = [];
        $this->reboundCallbacks = [];
        $this->resolvingCallbacks = [];
        $this->afterResolvingCallbacks = [];
        $this->globalResolvingCallbacks = [];
        $this->buildStack = [];
    }

    /**
     * 获取应用的命名空间,通过autoload.psr-4
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    public function getNamespace()
    {
        if (! is_null($this->namespace)) {
            return $this->namespace;
        }

        $composer = json_decode(file_get_contents(base_path('composer.json')), true);

        foreach ((array) data_get($composer, 'autoload.psr-4') as $namespace => $path) {
            foreach ((array) $path as $pathChoice) {
                if (realpath(app_path()) == realpath(base_path().'/'.$pathChoice)) {
                    return $this->namespace = $namespace;
                }
            }
        }

        throw new RuntimeException('Unable to detect application namespace.');
    }
}
