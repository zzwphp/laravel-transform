<?php
/**
 * Created by PhpStorm.
 * User: zhangzhenwei
 * Date: 2019/8/26
 * Time: 20:14
 */
if (!function_exists('response_transform')) {
    /**
     *功能:转换数据格式
     * transform
     *
     * @param $transform
     * @param $data
     *
     * @return array
     */
    function response_transform($transform, $data)
    {

        return (new \Ritin\LaravelTransform\Transformer(new $transform))->transform($data);
    }
}