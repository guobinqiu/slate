<?php

namespace  Jili\ApiBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class PasswordRegex extends Constraint
{
    public $invalid    = 'password_is_invalid';
    public $required   = 'password_is_required';
    public $min_length = 'password_is_too_short';
    public $max_length = 'password_is_too_long';
}
