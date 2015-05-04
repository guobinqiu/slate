<?php

namespace  Jili\ApiBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;;

/**
 * @Annotation
 */
class DuomaiApiOrdersPushChecksumValidator extends ConstraintValidator
{

    /**
     * 
     * @abstract: 关于 checksum 的说明：
     * checksum = MD5（string + hash）
     * 其中 string 为 上表格中除去checksum外所有参数，按照参数首字母升序排列后的 “参数的值”拼接的结果。
     * 注意：表格里面的数据根据需求可能会增加或者减少。对于接口开发人员，我们建议校验checksum 的代码逻辑如下：
     * 
     * 获取到的请求后
     * 1.先提取 checksum , id 
     * 2.将剩下的数据 按照数组索引首字母排序
     * 3.将排序后的数组数据 按照 value1 + value2 + value3 +　．．． 的顺序拼接得到 string
     * 4.校验 checksum 是否等于 md5( string + hash ）
     * 
     * Php 代码如下：
     * $hash = ''; // 接口密钥
     * $query= $_REQUEST;
     * $checksum = $query['checksum'];
     * $id = $query['id'];
     * unset($query['checksum'],$query['id']);
     * ksort($query);
     * $localsum = md5(join('',  array_values($query)).$hash);
     * 
     * 如果 $localsum == $checksum 即为合法的推送
     */
    public function validate($value, Constraint $constraint)
    {
        $hash = $value['hash']; // 接口密钥
        $query= $value['request'];

        $checksum = $query['checksum'];
        $id = $query['id'];

        unset($query['checksum'],$query['id']);

        ksort($query);

        $localsum = md5(join('',  array_values($query)).$hash);

        if ($localsum !== $checksum ) {
            $this->context->addViolation($constraint->message, array( '%string%'=>$checksum ));
        }
    }
}
