<?php

/**
 * User: phil.shu
 * Date: 2017/12/29
 * Time: 上午9:42
 */

namespace Super\Tests\Cache;

use PHPUnit\Framework\TestCase;
use Super\Api\Encryption\EncrypterApi;
use Super\Encryption\Encrypter;

class EncrypterTest extends TestCase
{
    private $key = '';

    public function setUp()
    {
        $this->key = 'NGQxNmUwMjM4M2Y0';

    }


    public function testEncrypt()
    {

        $obj = new Encrypter($this->key);
        $encrypt = $obj->encrypt("test");
        $this->assertNotNull($encrypt);

        $decrypt = $obj->decrypt($encrypt);
        $this->assertEquals("test", $decrypt);

    }

    public function testDecrypt()
    {

        $obj = new Encrypter($this->key);
        $encrypt = $obj->encrypt("bbb");
        $this->assertNotNull($encrypt);

        $decrypt = $obj->decrypt($encrypt);
        $this->assertEquals("bbb", $decrypt);

    }


}