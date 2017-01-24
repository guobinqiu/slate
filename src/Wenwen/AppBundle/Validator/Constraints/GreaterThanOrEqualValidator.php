<?php

namespace Wenwen\AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class GreaterThanOrEqualValidator extends ConstraintValidator
{
    /**
     * {@inheritDoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if ($value < $constraint->value) {
            $this->context->addViolation($constraint->message, array('{{ compared_value }}' => $constraint->value));
        }
    }
}
