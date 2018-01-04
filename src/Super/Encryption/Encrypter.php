<?php

namespace Super\Encryption;

use RuntimeException;
use Super\Api\Encryption\EncrypterApi;

class Encrypter implements EncrypterApi
{


    /**
     * 秘钥的key
     * @var
     */
    protected $key;

    /**
     * 秘钥的算法
     * @var
     */
    protected $cipher;

    /**
     * 秘钥加密
     * 描述:
     *  当前只支持AES 16位和AES 32位, AES : 高级加密标准(Advanced Encryption Standard)
     * @param $key
     * @param string $cipher
     * @throws  \Exception
     */
    public function __construct($key, $cipher = 'AES-128-CBC')
    {

        if ($this->validateCipher($key, $cipher)) {
            $this->key = $key;
            $this->cipher = $cipher;
        } else {
            throw new \Exception("The only support AES-128-CBC and  AES-256-CBC chiper");
        }
    }


    /**
     * 验证秘钥的操作
     * @param $key
     * @param $cipher
     * @return bool
     */
    protected function validateCipher($key, $cipher)
    {
        $len = mb_strlen($key, '8bit');
        if ($len === 16 && $cipher === 'AES-128-CBC') {
            return true;
        }

        if ($len === 32 && $cipher === 'AES-256-CBC') {
            return true;
        }

        return false;
    }

    /**
     * 给值进行加密操作
     * 描述:
     * 1. 使用openssl 加密,并制定加密的算法
     * 2. 对于加密后的值进行base64进行加密操作
     * 3. 返回加密后的结果
     *
     * string openssl_encrypt ( string $data ,
     *  string $method , string $key [, int $options = 0 [, string $iv = "" [, string &$tag = NULL [, string $aad = "" [, int $tag_length = 16 ]]]]] )
     *
     * @param $value
     * @param bool $serialize
     * @throws
     * @return mixed
     *
     * ps :
     * 初步完成了基本的加密和解密的操作。
     */
    public function encrypt($value, $serialize = true)
    {
        $iv = random_bytes(16);
        $value = \openssl_encrypt(
            $serialize ? serialize($value) : $value,
            $this->cipher,
            $this->key, 0,
            $iv);

        if ($value == false) {
            throw new \Exception("Could not encrypt the data!");
        }

        $iv = base64_encode($iv);
        $tmp = compact('iv', 'value');
        $json = json_encode($tmp);
        if (! is_string($json)) {
            throw new \Exception('Could not encrypt the data.');
        }
        return base64_encode($json);
    }

    /**
     * 解密的操作
     * @param $payload
     * @param bool $unserialize
     * @throws
     * @return mixed
     */
    public function decrypt($payload, $unserialize = true)
    {

        $payloadData = json_decode( base64_decode($payload), true);
        if (!$this->validPayload($payloadData)) {
            throw new \Exception("The payload Data is invalid");
        }
        $iv = base64_decode($payloadData['iv']);
        $encryptValue = $payloadData['value'];
        $data = \openssl_decrypt($encryptValue, $this->cipher, $this->key, 0, $iv);
        if ($data === false) {
            throw new \Exception("Could not decrypt the data!");
        }

        return $unserialize ? unserialize($data) : $data ;
    }

    /**
     * Create a MAC for the given value.
     *
     * @param  string  $iv
     * @param  mixed  $value
     * @return string
     */
    protected function hash($iv, $value)
    {
        return hash_hmac('sha256', $iv.$value, $this->key);
    }

    /**
     * 验证后加密后的数据
     *
     * @param  mixed $payload
     * @return bool
     */
    protected function validPayload($payload)
    {
        return is_array($payload) && isset(
            $payload['iv'], $payload['value']
        );
    }

    /**
     * @return mixed
     */
    public function getCipher()
    {
        return $this->cipher;
    }


    /**
     * @param mixed $cipher
     * @return Encrypter
     */
    public function setCipher($cipher)
    {
        $this->cipher = $cipher;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param mixed $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }


}
