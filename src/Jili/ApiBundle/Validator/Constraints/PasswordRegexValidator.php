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
        
        if(empty($value) ) {
            $this->context->addViolation($constraint->required);
            return false;
        }

        $length = strlen($value);
        if ( $length < 5 ) {
            $this->context->addViolation($constraint->min_length);
            return false;
        } else if($length > 100) {
            $this->context->addViolation($constraint->max_length);
            return false;
        }

        if (! preg_match('/^.*(?=.*?[A-Za-z])(?=.*?[0-9]).*$/', $value, $matches)) {
            $this->context->addViolation($constraint->invalid);
            return false;
        }
    }
}
