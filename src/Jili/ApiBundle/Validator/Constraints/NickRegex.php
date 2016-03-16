<?php

namespace  Jili\ApiBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class NickRegex extends Constraint
{
    public $message = '昵称不能含特殊符号';
}
