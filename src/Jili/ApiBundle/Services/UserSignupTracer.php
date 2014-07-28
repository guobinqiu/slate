<?php
namespace Jili\ApiBundle\Services;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpKernel\Log\LoggerInterface;

/**
 * 
 **/
class UserSignUpTracer
{
    private $user_manager;
    private $logger;
    private $session;
    private $user_source_logger;

    /**
     *  写sign up access  file.
     * TODO: check  a cookie validation for security.
     * @param $params array( '
     */
    function log(){
        $logger = $this->logger;

        $sessions = $this->session;
        if ( $sessions->has('source_route') ) {
            $messages = $sessions->get('source_route');
            $this->user_source_logger->info($messages);
        } else {
            $logger->debug('{jarod}'. implode(':', array(__LINE__, __CLASS__) ). var_export( $sessions , true) );
        }
        return $this;
    }           

    /**
     * 写user_signup_route
     * @params $params array(  'user_id'=> ) 
     */
    function signed(array $params ) 
    {
        $logger = $this->logger;
        if ($this->session->has('source_route') && isset($params ['user_id'] ) ) {
            $params ['source_route'] =  $this->session->get('source_route');
            $this->user_manager->setRegistrationRoute($params);
        } 
        return $this;
    }

    /**
     * refresh the session of  key 'source_route' on each request .
     * @param: array $params  array('spm'=>)
     **/
    public function refreshRouteSession( array $params ) 
    {
        if( isset($params['spm']) && ! empty( $params['spm'])) {
            $this->session->set('source_route', $params['spm']);
        }
        return $this;
    }
    /**
     *
     * @param  
     * @return the sessoin['source_route'], or an empty string otherwise.
     */
    public function getRouteSession() {
        $session = $this->session;
        if( $session->has(('source_route') )) {
            return  $session->get('source_route');
        }
        return '';
    }


    public function setSession($session) {
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


    public function setUserManager( $user_manager) {
        $this->user_manager = $user_manager;
    }

}
?>
