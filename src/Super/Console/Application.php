<?php

namespace Super\Console;

use Closure;
use Super\Api\Events\Dispatcher;
use Symfony\Component\Process\ProcessUtils;
use Super\Api\Container\Container;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Application as SymfonyApplication;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Super\Api\Console\Application as ApplicationContract;

class Application extends SymfonyApplication implements ApplicationContract
{
    /**
     * app 实例
     *
     * @var \Super\Api\Container\Container
     */
    protected $app;

    /**
     * 在命令行之前的输出
     *
     * @var \Symfony\Component\Console\Output\BufferedOutput
     */
    protected $lastOutput;

    /**
     * 控制台准备
     *
     * @var array
     */
    protected static $bootstrappers = [];

    /**
     * 创建控制台
     *
     * @param  \Super\Api\Container\Container  $laravel
     * @param  \Super\Api\Events\Dispatcher  $events
     * @param  string  $version
     * @return void
     */
    public function __construct(Container $app, Dispatcher $events, $version)
    {
        parent::__construct('Super Framework', $version);

        $this->app = $app;
        $this->setAutoExit(false);
        $this->setCatchExceptions(false);

        $events->dispatch(new Events\ArtisanStarting($this));

        $this->bootstrap();
    }

    /**
     * 判断是否php执行
     *
     * @return string
     */
    public static function phpBinary()
    {
        return ProcessUtils::escapeArgument((new PhpExecutableFinder)->find(false));
    }

    /**
     * 判断是否Artisan 命令执行
     *
     * @return string
     */
    public static function artisanBinary()
    {
        return defined('ARTISAN_BINARY') ? ProcessUtils::escapeArgument(ARTISAN_BINARY) : 'artisan';
    }

    /**
     * 格式化命令
     *
     * @param  string  $string
     * @return string
     */
    public static function formatCommandString($string)
    {
        return sprintf('%s %s %s', static::phpBinary(), static::artisanBinary(), $string);
    }

    /**
     * 准备启动
     *
     * @param  \Closure  $callback
     * @return void
     */
    public static function starting(Closure $callback)
    {
        static::$bootstrappers[] = $callback;
    }

    /**
     * 控制台的根启动
     *
     * @return void
     */
    protected function bootstrap()
    {
        foreach (static::$bootstrappers as $bootstrapper) {
            $bootstrapper($this);
        }
    }

    /**
     * 清理启动的准备信息
     *
     * @return void
     */
    public static function forgetBootstrappers()
    {
        static::$bootstrappers = [];
    }

    /**
     * 执行Artisan命令
     *
     * @param  string  $command
     * @param  array  $parameters
     * @param  \Symfony\Component\Console\Output\OutputInterface  $outputBuffer
     * @return int
     */
    public function call($command, array $parameters = [], $outputBuffer = null)
    {
        $parameters = collect($parameters)->prepend($command);

        $this->lastOutput = $outputBuffer ?: new BufferedOutput;

        $this->setCatchExceptions(false);

        $result = $this->run(new ArrayInput($parameters->toArray()), $this->lastOutput);

        $this->setCatchExceptions(true);

        return $result;
    }

    /**
     * 输出
     *
     * @return string
     */
    public function output()
    {
        return $this->lastOutput ? $this->lastOutput->fetch() : '';
    }

    /**
     * 把命令加入到控制台中
     *
     * @param  \Symfony\Component\Console\Command\Command  $command
     * @return \Symfony\Component\Console\Command\Command
     */
    public function add(SymfonyCommand $command)
    {
        if ($command instanceof Command) {
            $command->setApp($this->app);
        }

        return $this->addToParent($command);
    }

    /**
     * 增加命令到父命令行中
     *
     * @param  \Symfony\Component\Console\Command\Command  $command
     * @return \Symfony\Component\Console\Command\Command
     */
    protected function addToParent(SymfonyCommand $command)
    {
        return parent::add($command);
    }

    /**
     * 解析命令
     *
     * @param  string  $command
     * @return \Symfony\Component\Console\Command\Command
     */
    public function resolve($command)
    {
        return $this->add($this->app->make($command));
    }

    /**
     * 批量解析命令
     *
     * @param  array|mixed  $commands
     * @return $this
     */
    public function resolveCommands($commands)
    {
        $commands = is_array($commands) ? $commands : func_get_args();

        foreach ($commands as $command) {
            $this->resolve($command);
        }

        return $this;
    }

    /**
     * 增加环境变量到每个可用命令
     *
     * @return \Symfony\Component\Console\Input\InputDefinition
     */
    protected function getDefaultInputDefinition()
    {
        return tap(parent::getDefaultInputDefinition(), function ($definition) {
            $definition->addOption($this->getEnvironmentOption());
        });
    }

    /**
     * 获取全局的环境变量操作
     *
     * @return \Symfony\Component\Console\Input\InputOption
     */
    protected function getEnvironmentOption()
    {
        $message = 'The environment the command should run under';

        return new InputOption('--env', null, InputOption::VALUE_OPTIONAL, $message);
    }

    /**
     * Get the Laravel application instance.
     *
     * @return \Super\Api\Foundation\Application
     */
    public function getApp()
    {
        return $this->app;
    }
}
