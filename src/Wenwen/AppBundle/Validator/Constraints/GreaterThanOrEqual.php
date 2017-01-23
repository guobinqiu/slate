<?php

namespace Wenwen\AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class GreaterThanOrEqual extends Constraint
{
    public $message = 'This value should be greater than or equal to {{ compared_value }}.';
}
