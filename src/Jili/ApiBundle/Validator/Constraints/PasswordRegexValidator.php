<?php

namespace  Jili\ApiBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;;

/**
 * @Annotation
 */
class PasswordRegexValidator extends ConstraintValidator
{

    public function validate($value, Constraint $constraint)
    {
        if (!preg_match('/^[0-9A-Za-z_]{6,20}$/', $value, $matches)) {
            $this->context->addViolation($constraint->message);
        }
    }
}
