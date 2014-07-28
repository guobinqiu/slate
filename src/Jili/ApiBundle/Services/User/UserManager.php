<?php
namespace Jili\ApiBundle\Services\User;

use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Jili\ApiBundle\Entity\UserSignUpRoute;
use Doctrine\ORM\EntityManager;

class UserManager 
{

    private $em;
    private $logger;

    /**
     * @param: $params = array('user_id'=> ,'source_route'); 
     */
    public function setRegistrationRoute(array $params ) 
    {

        $logger = $this->logger;
        if ( isset($params['source_route']) && isset($params ['user_id'] ) ) {
            // new a log table
            $userSignUpRoute = new UserSignUpRoute();
            $userSignUpRoute->setUserId($params['user_id']);
            $userSignUpRoute->setSourceRoute($params['source_route']);
            $em = $this->em;
            $em->persist($userSignUpRoute);
            $em->flush();
            $logger->debug('{jarod}'. implode(':', array(__LINE__, __CLASS__,'') ) );
        } 
        return $this;
    } 



    public function setLogger(  LoggerInterface $logger) {
        $this->logger = $logger;
        return $this;
    }


    public function setEntityManager( EntityManager $em) {
        $this->em= $em;
    }


}
