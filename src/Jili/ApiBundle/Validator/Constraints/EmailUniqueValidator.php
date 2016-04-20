<?php

namespace  Jili\ApiBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Doctrine\ORM\EntityManager;

/**
 * @Annotation
 */
class EmailUniqueValidator extends ConstraintValidator
{

    private $em;
    public function validate($value, Constraint $constraint)
    {
        $user =$this->em->getRepository('JiliApiBundle:User')->findOneBy(array(
            'email'=>$value
        )); 

        if( empty($user) ) {
            return ;
        }

        if( $user->getIsEmailConfirmed() ) {
            $this->context->addViolation($constraint->message_email_unavailable, array());
        } else {
            $this->context->addViolation($constraint->message_email_reactive , array());
        }

    }

    public function setEntityManager(EntityManager $em) 
    {
        $this->em = $em;
        return $this;
    }

}
