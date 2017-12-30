<?php

namespace Super\Api\Filesystem;

interface Filesystem
{

    /**
     * 文件是否存在
     *
     * @param  string  $path
     * @return bool
     */
    public function exists($path);

    /**
     * 获取文件的内容
     *
     * @param  string  $path
     * @return string
     *
     * @throws \\Contracts\Filesystem\FileNotFoundException
     */
    public function get($path);

    /**
     *  在文件提里面写内容
     *
     * @param  string  $path
     * @param  string|resource  $contents
     * @param  string  $visibility
     * @return bool
     */
    public function put($path, $contents, $visibility = null);

    /**
     * 判断这个文件是否可以访问的
     *
     * @param  string  $path
     * @return string
     */
    public function getVisibility($path);

    /**
     * 设置可访问的权限
     *
     * @param  string  $path
     * @param  string  $visibility
     * @return void
     */
    public function setVisibility($path, $visibility);

    /**
     * 把数据
     *
     * @param  string  $path
     * @param  string  $data
     * @return int
     */
    public function prepend($path, $data);

    /**
     * 追加到一个文件里面
     *
     * @param  string  $path
     * @param  string  $data
     * @return int
     */
    public function append($path, $data);

    /**
     * 删除目录下的所有文件,批量删除目录下文件
     *
     * @param  string|array  $paths
     * @return bool
     */
    public function delete($paths);

    /**
     * 复制一个文件到另外一个文件里面
     *
     * @param  string  $from
     * @param  string  $to
     * @return bool
     */
    public function copy($from, $to);

    /**
     * 移动一个文件
     *
     * @param  string  $from
     * @param  string  $to
     * @return bool
     */
    public function move($from, $to);

    /**
     * 文件的大小统计
     *
     * @param  string  $path
     * @return int
     */
    public function size($path);

    /**
     * 文件的最后修改时间
     *
     * @param  string  $path
     * @return int
     */
    public function lastModified($path);

    /**
     * 获取文件或者目录下的所有文件列表信息
     *
     * @param  string|null  $directory
     * @param  bool  $recursive
     * @return array
     */
    public function files($directory = null, $recursive = false);

    /**
     * 显示所有的文件信息
     *
     * @param  string|null  $directory
     * @return array
     */
    public function allFiles($directory = null);

    /**
     * 显示指定目录下的所有的目录
     *
     * @param  string|null  $directory
     * @param  bool  $recursive
     * @return array
     */
    public function directories($directory = null, $recursive = false);

    /**
     * 获取目录地址的所有目录
     *
     * @param  string|null  $directory
     * @return array
     */
    public function allDirectories($directory = null);

    /**
     * 创建目录地址
     * @param $path
     * @param int $mode
     * @param bool $recursive
     * @param bool $force
     * @return mixed
     */
    public function makeDirectory($path ,  $mode = 0755, $recursive = false, $force = false);

    /**
     * 删除一个目录地址
     *
     * @param  string  $directory
     * @return bool
     */
    public function deleteDirectory($directory);
}
