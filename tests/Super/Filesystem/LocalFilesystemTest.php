<?php
/**
 * User: phil.shu
 * Date: 2017/12/29
 * Time: 下午2:17
 */

namespace Super\Filesystem;

use PHPUnit\Framework\TestCase;
use Super\Filesystem;

class FilesystemTest extends TestCase
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
     * 目录的增删改查
     */
    public function testCrudDir()
    {

        $dir = "testdir";
        $path = $this->tempDir . DIRECTORY_SEPARATOR . $dir;
        $fs = new LocalFilesystem();
        if (!$fs->isDirectory($path)) {
            $fs->makeDirectory($path);
        }
        $this->assertTrue($fs->exists($path));

        $newName = 'newDir';
        $fs->move($path, $this->tempDir . DIRECTORY_SEPARATOR . $newName);
        $this->assertEquals($newName, $fs->name($this->tempDir . DIRECTORY_SEPARATOR . $newName));

        $fs->deleteDirectory($path);
        $this->assertFalse($fs->exists($path));

    }

    /**
     * 对文件的CRUD的操作
     */
    public function testCrudFile()
    {

        $fileName = 'f1.txt';
        $path = $this->tempDir . DIRECTORY_SEPARATOR . $fileName;

        $fs = new LocalFilesystem();
        $fs->put($path, "123");
        $this->assertTrue($fs->exists($path));

        $newFile = 'nf1.txt';
        $newPath = $this->tempDir . DIRECTORY_SEPARATOR . $newFile;
        $fs->move($path, $newPath);
        $this->assertTrue($fs->exists($newPath));

        $fs->delete($newPath);
        $this->assertFalse($fs->exists($newPath));
    }


    /**
     * 对文件的内容进行CRUD
     */
    public function testCrudFileContext()
    {

        $fileName = 'f2.txt';
        $path = $this->tempDir . DIRECTORY_SEPARATOR . $fileName;
        $fs = new LocalFilesystem();
        $fs->put($path, "666666");
        $this->assertEquals("666666", $fs->get($path));

        $fs->put($path, "333");
        $this->assertEquals("333", $fs->get($path));

        $fs->append($path, "5");
        $this->assertEquals("3335", $fs->get($path));

        $fs->prepend($path, "6");
        $this->assertEquals("63335", $fs->get($path));

        $fs->put($path, "");
        $this->assertEquals("", $fs->get($path));
        $fs->delete($path);
        $this->assertFalse($fs->exists($path));
    }


    /**
     * 对文件的权限进行CRUD
     */
    public function testCrudPem()
    {

        $fileName = "f3.txt";
        $path = $this->tempDir . DIRECTORY_SEPARATOR . $fileName;
        $fs = new LocalFilesystem();
        $fs->put($path, "666666");
        $this->assertEquals("666666", $fs->get($path));
        $fs->chmod($path, 0444);

        $this->assertFalse($fs->isWritable($path));
        $fs->chmod($path, 0777);
        $this->assertTrue($fs->isWritable($path));
        $fs->delete($path);
        $this->assertFalse($fs->exists($path));
    }

    /**
     * 对于多目录的操作
     * @todo
     */
    public function testCRUDManyDirectory()
    {

//        $dir1 = 'dir1';
//        $dir2 = 'dir2';
//        $path1 = $this->tempDir . DIRECTORY_SEPARATOR . $dir1;
//        $path2 = $this->tempDir . DIRECTORY_SEPARATOR . $dir2;
//
//        $fs = new LocalFilesystem();
//        $fs->delete($path1);
//        $fs->delete($path2);
//        if(!is_dir($path1))
//        {
//            $fs->makeDirectory($path1);
//        }
//
//        if(!is_dir($path2))
//        {
//            $fs->makeDirectory($path2);
//        }
//
//        $data = $fs->allDirectories($this->tempDir);
//        $this->assertContains($path1, $data);
//        $this->assertContains($path2, $data);
//
//        //修改所有的目录的信息
//        foreach ($data as $key => $val) {
//            $fs->move($val, $val . 'new');
//        }
//
//        $this->assertTrue($fs->isDirectory($path1 . 'new'));
//        $this->assertTrue($fs->isDirectory($path2 . 'new'));
//
//        $this->assertTrue($fs->delete($path1 . 'new'));
//        $this->assertTrue($fs->delete($path2 . 'new'));
//        $this->assertFalse($fs->exists($path1 . 'new'));
//        $this->assertFalse($fs->exists($path2 . 'new'));

    }

    /**
     * 对多文件的CRUD的操作
     */
    public function testManyFile()
    {

        $file1 = 'f1.txt';
        $file2 = 'f2.txt';
        $path1 = $this->tempDir . DIRECTORY_SEPARATOR . $file1;
        $path2 = $this->tempDir . DIRECTORY_SEPARATOR . $file2;

        $fs = new LocalFilesystem();
        $fs->put($path1, '');
        $fs->put($path2, '');

        $data = $fs->allFiles($this->tempDir);
        $this->assertContains($path1, $data);
        $this->assertContains($path2, $data);


        $newPath1 = $this->tempDir . DIRECTORY_SEPARATOR . 'new' . $file1;
        $newPath2 = $this->tempDir . DIRECTORY_SEPARATOR . 'new' . $file2;

        //修改所有的目录的信息
        $fs->move($path1, $newPath1);
        $fs->move($path2, $newPath2);

        $this->assertTrue($fs->isFile($newPath1));
        $this->assertTrue($fs->isFile($newPath2));

        $this->assertTrue($fs->delete($newPath1));
        $this->assertTrue($fs->delete($newPath2));
        $this->assertFalse($fs->exists($newPath1));
        $this->assertFalse($fs->exists($newPath2));

    }

}