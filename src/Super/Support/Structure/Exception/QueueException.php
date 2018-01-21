<?php
/**
 * User: phil.shu
 * Date: 2018/1/21
 * Time: 下午9:35
 */

namespace Super\Support\Structure\Exception;


class QueueException extends \Exception
{

    protected  $code = 10001;
    public function __construct($message = "" , $code = '')
    {

        $this->code = $code;
        $message = $message . " queue exception!";
        parent::__construct($message);
    }


}