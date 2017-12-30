<?php

namespace Super\Filesystem;

use FilesystemIterator;
use Super\Api\Filesystem\FileNotFoundException;
use Symfony\Component\Finder\Finder;


/**
 * User: phil.shu
 * Date: 2017/12/29
 * Time: 下午3:17
 */
class LocalFilesystem implements \Super\Api\Filesystem\Filesystem
{

    /**
     * 创建一个目录地址
     *
     * @param string $path
     * @param int $mode
     * @param bool $recursive
     * @param bool $force
     * @return  bool
     */
    public function makeDirectory($path, $mode = 0755, $recursive = false, $force = false)
    {

        if ($force) {
            return @mkdir($path, $mode, $recursive);
        }
        return mkdir($path, $mode, $recursive);
    }

    /**
     * 删除一个目录地址,需要递归的删除文件的目录信息
     *
     * @param  string $directory
     *
     * @return bool
     */
    public function deleteDirectory($directory, $preserve = false)
    {
        if (!$this->isDirectory($directory)) {
            return false;
        }

        $items = new FilesystemIterator($directory);
        foreach ($items as $val) {
            if (is_dir($val) && !is_link($val)) {
                $this->deleteDirectory($val->getPathname());
            } else {
                $this->delete($val->getPathName());
            }
        }

        if ($preserve) {
            @rmdir($directory);
        }

        return true;

    }


    /**
     *  在文件提里面写内容
     *
     * @param  string $path
     * @param  string|resource $contents
     * @param  bool $lock
     * @return bool
     */
    public function put($path, $contents, $lock = false)
    {
        return file_put_contents($path, $contents, $lock ? LOCK_EX : 0);
    }


    /**
     * 文件是否存在
     *
     * @param  string $path
     * @return bool
     */
    public function exists($path)
    {
        return file_exists($path);
    }

    /**
     * 获取文件的内容
     * @param string $path
     * @param bool $lock
     * @throws FileNotFoundException
     * @return string
     */
    public function get($path, $lock = false)
    {
        if (!$this->isFile($path)) {
            throw new FileNotFoundException("File does not exist at path {$path}");
        }
        if ($lock) {
            return $this->sharedGet($path);
        }
        return file_get_contents($path);
    }

    /**
     * 获取共享的内容信息
     *
     * 读锁(LOCK_SH)和写锁(LOCK_EX)
     * @param  string $path
     * @return string
     */
    public function sharedGet($path)
    {
        $contents = '';

        $handle = fopen($path, 'rb');

        if ($handle) {
            try {

                //在读取的时候加上锁机制
                if (flock($handle, LOCK_SH)) {
                    clearstatcache(true, $path);

                    $contents = fread($handle, $this->size($path) ?: 1);

                    //释放锁
                    flock($handle, LOCK_UN);
                }
            } finally {
                fclose($handle);
            }
        }

        return $contents;
    }

    /**
     * 判断这个文件是否可以访问的
     *
     * @param  string $path
     * @return string
     */
    public function getVisibility($path)
    {
        return is_readable($path);
    }

    /**
     * 设置可访问的权限
     *
     * @param  string $path
     * @return void
     */
    public function setVisibility($path, $visiblity = true)
    {

        return is_writable($path);
    }

    /**
     * 把数据前插入
     *
     * @param  string $path
     * @param  string $data
     * @return int
     */
    public function prepend($path, $data)
    {
        if ($this->exists($path)) {
            return $this->put($path , $data . $this->get($path)) ;
        }
        return $data;
    }

    /**
     * 追加到一个文件里面
     *
     * @param  string $path
     * @param  string $data
     * @return int
     */
    public function append($path, $data)
    {
        return file_put_contents($path, $data, FILE_APPEND);
    }

    /**
     * 删除目录下的所有文件,批量删除目录下文件
     *
     * @param  string|array $paths
     * @return bool
     */
    public function delete($paths)
    {
        $paths = is_array($paths) ? $paths : func_get_args();

        $success = true;
        foreach ($paths as $key => $path) {
            if (!$this->exists($path)) {
                $success = false;
            }

            try {

                if (!unlink($path)) {
                    $success = false;
                }
            } catch (\Exception $e) {
                $success = false;
            }
        }
        return $success;
    }

    /**
     * 复制一个文件到另外一个文件里面
     *
     * @param  string $from
     * @param  string $to
     * @return bool
     */
    public function copy($from, $to)
    {
        return copy($from, $to);
    }

    /**
     * 移动一个文件
     *
     * @param  string $from
     * @param  string $to
     * @return bool
     */
    public function move($from, $to)
    {
        return rename($from, $to);
    }


    /**
     * 创建一个硬链接或者目录
     *
     * @param  string $target
     * @param  string $link
     * @return void
     */
    public function link($target, $link)
    {
        if (!$this->windows_os()) {
            return symlink($target, $link);
        }

        $mode = $this->isDirectory($target) ? 'J' : 'H';

        exec("mklink /{$mode} \"{$link}\" \"{$target}\"");
    }

    /**
     * 文件的大小统计
     *
     * @param  string $path
     * @return int
     */
    public function size($path)
    {
        return filesize($path);
    }

    /**
     * 文件的最后修改时间
     *
     * @param  string $path
     * @return int
     */
    public function lastModified($path)
    {
        return filemtime($path);
    }

    /**
     * 获取文件或者目录下的所有文件列表信息
     *
     * @param  string|null $directory
     * @param  bool $recursive
     * @return array
     */
    public function files($directory = null, $recursive = false)
    {

        if (!$recursive) {
            $data = [];
            $files = scandir($directory);
            foreach ($files as $key => $file) {
                if (!is_dir($file)) {
                    $data[] = $file;
                }
            }
            return $data;
        }

        $glob = glob($directory . DIRECTORY_SEPARATOR . '*');

        if ($glob === false) {
            return [];
        }
        return array_filter($glob, function ($file) {
            return filetype($file) == 'file';
        });

    }

    /**
     * 根据正则的方式来查找文件
     * @param $pattern
     * @param int $flags
     * @return array
     */
    public function glob($pattern, $flags = 0)
    {
        return glob($pattern, $flags);
    }

    /**
     * 显示所有的文件信息
     *
     * @param  string|null $directory
     * @return array
     */
    public function allFiles($directory = null)
    {
        return $this->files($directory, true);
    }

    /**
     * 显示指定目录下的所有的目录
     *
     * @param  string|null $directory
     * @param  bool $recursive
     * @return array
     */
    public function directories($directory = null, $recursive = false)
    {
        $directories = [];

        foreach (Finder::create()->in($directory)->directories()->depth(0) as $dir) {
            $directories[] = $dir->getPathname();
        }

        return $directories;
    }

    /**
     * 获取目录地址的所有目录
     *
     * @param  string|null $directory
     * @return array
     */
    public function allDirectories($directory = null)
    {
       return  $this->directories($directory, true);
    }


    /**
     * 获取文件或者目录的名称
     */
    public function name($path)
    {

        return pathinfo($path, PATHINFO_FILENAME);
    }

    /**
     * Determine if the given path is a directory.
     *
     * @param  string $directory
     * @return bool
     */
    public function isDirectory($directory)
    {
        return is_dir($directory);
    }

    /**
     * Determine if the given path is readable.
     *
     * @param  string $path
     * @return bool
     */
    public function isReadable($path)
    {
        return is_readable($path);
    }

    /**
     * Determine if the given path is writable.
     *
     * @param  string $path
     * @return bool
     */
    public function isWritable($path)
    {
        return is_writable($path);
    }

    /**
     * Determine if the given path is a file.
     *
     * @param  string $file
     * @return bool
     */
    public function isFile($file)
    {
        return is_file($file);
    }

    /**
     * Get or set UNIX mode of a file or directory.
     *
     * @param  string $path
     * @param  int $mode
     * @return mixed
     */
    public function chmod($path, $mode = null)
    {
        if ($mode) {
            return chmod($path, $mode);
        }

        return substr(sprintf('%o', fileperms($path)), -4);
    }

    protected function windows_os()
    {
        return strtolower(substr(PHP_OS, 0, 3)) === 'win';
    }

}