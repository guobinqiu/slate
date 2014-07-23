<?php
namespace Jili\ApiBundle\Services;

use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Jili\ApiBundle\Entity\UserSignUpRoute;
use Jili\ApiBundle\Entity\User;

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
     * TODO: check  a cookie validation for security.
     */
    function log(Request $request){
        $logger = $this->logger;
        $logger->debug('{jarod}'. implode(':', array(__LINE__, __CLASS__) ). var_export( $request->cookies , true) );
        $cookies = $request->cookies->all();
        $messages = array();
        $messages[] = isset($cookies['source_route']) ? $cookies['source_route']: 'source_route' ;
        $messages[] = isset($cookies['pv']) ? $cookies['pv']: 'pv' ;
        $messages[] = isset($cookies['pv_unique']) ? $cookies['pv_unique']: 'pv_unique' ;
        $message_tsv = implode("\t" , $messages);
        $this->user_source_logger->info($message_tsv);
        return $this;
    }           

    /**
     * 写user_signup_route
     */
    function signed(Request  $request,  User $user) 
    {
        $logger = $this->logger;
        $logger->debug('{jarod}'. implode(':', array(__LINE__, __CLASS__) ) );

        // new a log table
        $userSignUpRoute = new UserSignUpRoute();
        $userSignUpRoute->setUserId($user->getId());
        $userSignUpRoute->setSourceRoute($request->cookies->get('source_route'));
        $em = $this->em;
        $em->persist($userSignUpRoute);
        $em->flush();
        return $this;
    }

    public function setSession(  $session) {
        $this->session = $session;
        return $this;
    }

    public function setLogger(  LoggerInterface $logger) {
        $this->logger = $logger;
        return $this;
    }

    public function setUserSourceLogger(  LoggerInterface $logger) {
        $this->user_source_logger = $logger;
        return $this;
    }

    public function setEntityManager( EntityManager $em) {
        $this->em= $em;
    }

}
?>
