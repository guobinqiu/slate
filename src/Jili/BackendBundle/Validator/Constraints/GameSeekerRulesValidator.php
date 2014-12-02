<?php
namespace Jili\BackendBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class GameSeekerRulesValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        $b = explode(PHP_EOL, $value);
        foreach($b as $k => $v) {
            $v = trim($v);
            if( ! preg_match('/^\d+\:\d+(\:\d+(,\d+)*)*$/', $v) &&  ! preg_match('/^\d+\:\:\d+(,\d+)*$/', $v)) 
            {
                $this->context->addViolation($constraint->message, array('%string%' => $v));
            }
        }
    }
}
