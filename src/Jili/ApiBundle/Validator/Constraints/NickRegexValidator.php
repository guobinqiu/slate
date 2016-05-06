<?php

namespace  Jili\ApiBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * @Annotation
 */
class NickRegexValidator extends ConstraintValidator
{

    public function validate($value, Constraint $constraint)
    {
        if (!preg_match('/^[^\~\`\"\'\:\;\<\>\/\?\!\#\$\%\^\*\(\)\+\=\{\}\[\]]+$/u', $value, $matches)) {
            $this->context->addViolation($constraint->message);
        }
    }
}
