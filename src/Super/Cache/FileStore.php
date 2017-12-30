<?php
/**
 * User: phil.shu
 * Date: 2017/12/29
 * Time: 下午2:12
 */

namespace Super\Cache;


use Carbon\Carbon;
use Super\Api\Cache\Store;
use Super\Api\Filesystem\FileNotFoundException;
use Super\Api\Filesystem\Filesystem;

class FileStore implements Store
{
    use GetMultipleKeys;

    protected $files = null;
    protected $directory = null;

    public function __construct(Filesystem $files, $directory)
    {
        $this->files = $files;
        $this->directory = $directory;
    }

    /**
     * 根据Key 获取字符或数组
     *
     * @param  string|array $key
     * @return mixed
     */
    public function get($key)
    {
        $data = $this->getPayload($key);
        $return = $data['data'];
        return $return;
        //return "aaa";
    }

    protected function getPayload($key)
    {

        $data = [
            'data' => null,
            'time' => 0,
        ];
        $path = $this->path($key);

        if(!$this->files->exists($path)){
            return $data;
        }

        try {
            //获取过期的时间
            $expire = substr(
                $contents = $this->files->get($path, true), 0, 10
            );
        } catch (Exception $e) {

            return $data;
        }


        //判断是否缓存文件过期了
        if ($expire < Carbon::now()->getTimestamp()) {
            return $data;
        } else {
            $time = $expire - Carbon::now()->getTimestamp();
        }

        $value = $this->files->get($path);

        $data = unserialize(substr($value, 10));

        return compact('data', 'time');
    }

    /**
     * 存储一个缓存,并制定失效时间
     * 1. 确保缓存的目录是是可以创建的;
     * 2. 把过期的时间写入到文件头部,这样通过解析文件头部可以知道缓存的失效时间。
     *
     * @param  string $key
     * @param  mixed $value
     * @param  float|int $minutes
     * @return bool
     */
    public function put($key, $value, $minutes)
    {

        $path = $this->path($key);
        $this->ensureCacheDirectoryExist($path);
        $data = $this->expiration($minutes) . serialize($value);
        return $this->files->put($path, $data);
    }


    protected function ensureCacheDirectoryExist($path)
    {
        if (!$this->files->exists($path)) {
            $this->files->makeDirectory($path, 0777, true, true);
        }
    }

    protected function expiration($minutes)
    {

        $time = Carbon::now()->getTimestamp() + (int)$minutes * 60;

        //避免设置的过期时间特别的长,出现位数超过10的情况
        return $minutes === 0 || $time > 9999999999 ? 9999999999 : (int)$time;

    }


    /**
     * 根据key 获取缓存的路径
     * 需要考虑不同的操作系统分割符号是不一样的。
     * @param $key
     * @return string
     */
    protected function path($key)
    {
        $parts = array_slice(str_split($hash = sha1($key), 2), 0, 2);

        return $this->directory . '/' . implode('/', $parts) . '/' . $hash;
    }

    /**
     * 删除一个缓存
     *
     * @param  string $key
     * @return bool
     */
    public function forget($key)
    {
        $path = $this->path($key);
        return $this->files->delete($path);
    }

    /**
     * 删除所有的缓存
     *
     * @return bool
     */
    public function flush()
    {
        //配置的目录的缓存文件
        $this->files->deleteDirectory($this->directory);
    }


    /**
     * 对缓存的key数值增加
     *
     * @param  string $key
     * @param  mixed $value
     * @return int|bool
     */
    public function increment($key, $value = 1)
    {

        $data = $this->getPayload($key);
        return ((int)$data['data'] + $value);
    }

    /**
     * 对缓存的key数值减少
     *
     * @param  string $key
     * @param  mixed $value
     * @return int|bool
     */
    public function decrement($key, $value = 1)
    {
        $this->increment($key, -1 * $value);
    }

    /**
     * 设置这个缓存,不设置失效时间
     *
     * @param  string $key
     * @param  mixed $value
     * @return void
     */
    public function forever($key, $value)
    {
        return $this->put($key, $value, 0);
    }

    /**
     * 获取缓存的前缀
     *
     * @return string
     */
    public function getPrefix()
    {
        return '';
    }
}