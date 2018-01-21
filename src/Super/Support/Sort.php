<?php
/**
 * User: phil.shu
 * Date: 2018/1/15
 * Time: 下午11:33
 */

namespace Super\Support;


/**
 * 排序算法, 主要是满足不同场景的接口的使用
 *
 * Class Sort
 * @package Super\Support
 */
class Sort
{

    /**
     * 冒泡数组排序
     * 算法逻辑:
     * 1. 当前值跟后面的比较,如果比它大,则交换,否则不变。最后大的值都放到最后了。
     * 2. 遍历完所有的节点
     * @param array $arr 排序的数组
     * @return array 返回排序的数组
     */
    public function bubbleSort(array $arr)
    {

        if (empty($arr)) {
            return $arr;
        }

        $len = count($arr);
        for ($i = 0; $i < $len; $i++) {
            for ($j = $i + 1; $j < $len; $j++) {
                $current = $arr[$i];
                if ($current > $arr[$j]) {

                    //标号的值进行交换
                    $arr[$i] = $arr[$j];
                    $arr[$j] = $current;
                }
            }
        }
        return $arr;

    }

    /**
     * 插入排序的实现
     * 算法逻辑:
     * 1. 后面的元素跟当前元素的比较,如果后面元素比当前元素要小,则交换,然后所有的值向后移。
     * 2. 遍历所有的元素
     * @param array $arr
     * @return array
     */
    public function insertSort(array $arr)
    {

        if (empty($arr)) {
            return $arr;
        }
        $len = count($arr);
        for ($i = 0; $i < $len; $i++) {

            //存在比当前元素大的情况则前插入,否则后插入
            if ($arr[$i] > $arr[$i+1]) {
                for ($j = $i + 1; $j < $len; $j++) {
                    $arr[$j] = $arr[$i];
                }
                $arr[$i] = $arr[$i+1];
            }
        }
        return $arr;
    }

    /**
     * 快速排序的实现
     * @param array $arr
     * @return array
     */
    public function fastSort(array $arr)
    {

        return $arr;
    }

}