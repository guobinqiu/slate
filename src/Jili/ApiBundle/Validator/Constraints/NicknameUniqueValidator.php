<?php

namespace  Jili\ApiBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Doctrine\ORM\EntityManager;

/**
 * @Annotation
 */
class NicknameUniqueValidator extends ConstraintValidator
{

    private $em;
    public function validate($value, Constraint $constraint)
    {
        $user =$this->em->getRepository('JiliApiBundle:User')->findOneBy(array(
            'nick'=>$value
        )); 

        if( empty($user) ) {
            return ;
        }

        $this->context->addViolation($constraint->message_nickname_unavailable, array());

    }

    public function setEntityManager(EntityManager $em) 
    {
        $this->em = $em;
        return $this;
    }

}
