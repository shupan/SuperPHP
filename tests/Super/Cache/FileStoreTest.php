<?php

/**
 * User: phil.shu
 * Date: 2017/12/29
 * Time: 上午9:42
 */

namespace Super\Tests\Cache;

use PHPUnit\Framework\TestCase;
use Super\Api\Filesystem\FileNotFoundException;
use Super\Cache\ArrayStore;
use Super\Cache\FileStore;

class FileStoreTest extends TestCase
{
    private $tempDir;

    public function setUp()
    {

        $this->tempDir = __DIR__ . '/tmp';
        if (!is_dir($this->tempDir)) {
            mkdir($this->tempDir);
        }
    }

    /**
     * - 文件不存在
     * - 目录不存在
     * - 缓存过期
     * - 返回的内容是否合法
     * - 测试文件是否存在过期
     * - 删除文件后会提示文件不存在
     * - 测试删除文件
     * - 清空文件目录
     * - 空间文件目录失败
     * - 对于不存在的目录执行清空失败
     * @todo , 对文件的缓存,处理这一块有点麻烦
     */
    public function testCrudFileCache()
    {

//        $fs = $this->mockFilesystem();
//        $fs->expects($this->once())->method('get')->will($this->throwException(new FileNotFoundException()));
//        $store = new FileStore($fs, $this->tempDir);
//        $path = 'test/k1';
//        $store->get($path);
//        $store->put($path, "t1", 10);
//        $data = $store->get($path);
//        $this->assertEquals("t1", $data);
//        $store->forget($path);
//        $this->assertFalse($store->get($path));

        $files = $this->mockFilesystem();
        $files->expects($this->once())->method('get')->will($this->throwException(new FileNotFoundException()));
        $files->expects($this->once())->method('put')->will();
        $store = new FileStore($files, $this->tempDir);
        $value = $store->get('foo');
        $this->assertNull($value);

    }


    protected function mockFilesystem()
    {

        return $this->createMock('Super\Filesystem\LocalFilesystem');
    }

}