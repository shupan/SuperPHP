<?php

namespace Super\Api\Console;

interface Kernel
{
    /**
     * 接受控制台的命令行操作
     *
     * @param  \Symfony\Component\Console\Input\InputInterface  $input
     * @param  \Symfony\Component\Console\Output\OutputInterface  $output
     * @return int
     */
    public function handle($input, $output = null);

    /**
     * 根据名称和参数调用控制台操作
     *
     * @param  string  $command
     * @param  array  $parameters
     * @return int
     */
    public function call($command, array $parameters = []);

    /**
     * 把控制台的命令放入到队列中
     *
     * @param  string  $command
     * @param  array  $parameters
     * @return \Super\Foundation\Bus\PendingDispatch
     */
    public function queue($command, array $parameters = []);

    /**
     * 获取控制台所有注册的命令
     *
     * @return array
     */
    public function all();

    /**
     * 获取控制台输出的信息
     *
     * @return string
     */
    public function output();
}
