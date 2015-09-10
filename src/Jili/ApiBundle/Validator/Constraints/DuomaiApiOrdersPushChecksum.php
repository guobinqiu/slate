<?php

namespace  Jili\ApiBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class DuomaiApiOrdersPushChecksum extends Constraint
{
    public $message = 'Checksum  验证失败: 需要%local_checksum%, 得到 %request_checksum%';
}
