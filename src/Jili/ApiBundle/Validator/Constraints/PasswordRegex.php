<?php

namespace  Jili\ApiBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class PasswordRegex extends Constraint
{
    public $message = '用户密码为6-20个字符，不能含特殊符号';
}

