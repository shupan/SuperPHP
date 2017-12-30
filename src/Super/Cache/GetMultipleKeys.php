<?php
/**
 * User: phil.shu
 * Date: 2017/12/29
 * Time: 上午11:48
 */

namespace Super\Cache;


trait GetMultipleKeys
{

    /**
     * 可以获取多个key,如果返回的key里面是没有找到怎么会返回一个null值
     *
     * @param  array $keys
     * @return array
     */
    public function many(array $keys)
    {
        $data = [];
        foreach ($keys as $key => $val) {
            $data[] = $this->get($val);
        }
        return $data;
    }

    /**
     *  一次性存放很多的缓存的数组,并指定失效时间
     *
     * @param  array $values
     * @param  float|int $minutes
     * @return void
     */
    public function putMany(array $values, $minutes)
    {
        foreach ($values as $key => $val) {
            $this->put($key, $val, $minutes);
        }
    }
}