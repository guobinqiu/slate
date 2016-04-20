<?php

namespace  Jili\ApiBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class EmailUnique extends Constraint
{
    public $message_email_unavailable = 'user_email_has_been_activated';
    public $message_email_reactive    = 'user_email_has_not_been_activated';

    public function validatedBy() 
    {
        return 'user_validator_email_unique';
    }
}
