<?php
namespace Jili\ApiBundle\Services;

use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Jili\ApiBundle\Services\Logger\UserSourceLogger;

/**
 * 
 **/
class UserSignUpTracer
{
    

    private $em;
    private $logger;
    private $session;
    private $user_source_logger;

    /**
     *  写sign up access  file.
     */
    function log(Request $request){
        $logger = $this->logger;
        $logger->debug('{jarod}'. implode(':', array(__LINE__, __CLASS__) ) );
        //   $request->getCookie();
        $this->user_source_logger->getLogger()->debug('{user_source} .message');
        $this->user_source_logger->getLogger()->info('{user_source} .message');
        $this->user_source_logger->getLogger()->warning('{user_source} .message');
        $this->user_source_logger->getLogger()->err('{user_source} .message');
        $this->user_source_logger->getLogger()->crit('{user_source} .message');
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

    public function setUserSourceLogger(  UserSourceLogger $logger) {
        $this->user_source_logger = $logger;
        return $this;
    }

    public function setEntityManager( EntityManager $em) {
        $this->em= $em;
    }

}
?>
