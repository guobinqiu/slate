<?php

namespace  Jili\ApiBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class NicknameUnique extends Constraint
{
    public $message_nickname_unavailable = 'user_nickname_has_been_activated';

    public function validatedBy() 
    {
        return 'user_validator_nickname_unique';
    }
}
