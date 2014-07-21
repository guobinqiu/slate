<?php
namespace Jili\ApiBundle\Services;

use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;

/**
 * 
 **/
class UserSignUpTracer
{
    

    private $em;
    private $logger;
    private $session;


    /**
     *  写sign up access  file.
     */
    function log(Request $request){
        $logger = $this->logger;
        $logger->debug('{jarod}'. implode(':', array(__LINE__, __CLASS__) ) );
        //   $request->getCookie();
        //write log file
    }           

    /**
     * 写user_signup_route
     */
    function signed(Request  $request,  User $user) 
    {
        $logger = $this->logger;
        $logger->debug('{jarod}'. implode(':', array(__LINE__, __CLASS__) ) );
        // new a log table
    }

    public function setSession(  $session) {
        $this->session = $session;
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
?>
