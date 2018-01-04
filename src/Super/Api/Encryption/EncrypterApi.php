<?php
namespace Super\Api\Encryption;

/**
 * 加密/解密的方式
 * User: phil.shu
 * Date: 2018/1/4
 * Time: 下午4:06
 */
interface EncrypterApi
{

    /**
     * 给值进行加密操作
     * @param $value
     * @param bool $serialize
     * @return mixed
     */
    public function encrypt($value , $serialize = true);


    /**
     * 解密的操作
     * @param $payload
     * @param bool $unserialize
     * @return mixed
     */
    public function decrypt($payload , $unserialize = true);
}