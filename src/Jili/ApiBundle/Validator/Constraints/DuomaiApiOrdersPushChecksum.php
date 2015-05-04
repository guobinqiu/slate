<?php

namespace  Jili\ApiBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class DuomaiApiOrdersPushChecksum extends Constraint
{
    public $message = 'Checksum %string% 验证失败';
}
