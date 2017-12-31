<?php

namespace Super\Api\Console;

interface Application
{
    /**
     * 控制台输入命令操作
     *
     * @param  string  $command
     * @param  array  $parameters
     * @return int
     */
    public function call($command, array $parameters = []);

    /**
     * 输出控制台打印的结果
     *
     * @return string
     */
    public function output();
}
