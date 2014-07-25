<?php
namespace Jili\ApiBundle\Services;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
        if ( $request->cookies->has('source_route') ) {

            $messages = $request->cookies->get('source_route');
            $messages .= "\t".$request->cookies->get('pv', 'pv');
            $messages .= "\t".$request->cookies->get('pv_unique', 'pv_unique');
            $this->user_source_logger->info($messages);
        }
        return $this;
    }           

    /**
     * 写user_signup_route
     */
    function signed(Request  $request,  User $user) 
    {
        $logger = $this->logger;
        $logger->debug('{jarod}'. implode(':', array(__LINE__, __CLASS__,'') ) );
        if ($request->cookies->has('source_route')) {
            // new a log table
            $userSignUpRoute = new UserSignUpRoute();
            $userSignUpRoute->setUserId($user->getId());
            $userSignUpRoute->setSourceRoute($request->cookies->get('source_route'));
            $em = $this->em;
            $em->persist($userSignUpRoute);
            $em->flush();
        }
        $logger->debug('{jarod}'. implode(':', array(__LINE__, __CLASS__,'') ) );
        return $this;
    }

    /**
     * refresh the cookie on each request.
     * pv updated each time 
     * pv_unique  updated only once 
     * source_route , store the query param ?spm=
     * @param:  $request
     **/
    public function initCookies(Request $request, RedirectResponse $redirectResponse) {
        if( $request->query->has('spm') ) {
            $spm = $request->query->get('spm');
            $expire = time();

            $path = $this->router->getRouteCollection()->get('_user_reg')->getPath();

        $this->logger->debug('{jarod}'. implode(':', array(__LINE__, __CLASS__,'') ). var_export($path, true) );
            $path = substr($path, 1);
        $this->logger->debug('{jarod}'. implode(':', array(__LINE__, __CLASS__,'') ). var_export($path, true) );
;
            if(! $request->cookies->has('pv_unique_11') ) {
                $redirectResponse->headers->setCookie(new Cookie('pv_unique_11', md5('pv'. $spm. $expire ), $expire + 86400, $path, null, false, false ));
            }
// $expire will be ignore unless a litter longer. 
            $redirectResponse->headers->setCookie(new Cookie('source_route_11', $spm, $expire + 1 , $path, null, false, false ));
            $redirectResponse->headers->setCookie(new Cookie('pv_11', md5('pv'.$spm. $expire ), $expire  + 1 , $path, null, false, false ));
        }
        return $redirectResponse;
    }

    public function setSession(  $session) {
        $this->session = $session;
        return $this;
    }

    public function setRouter(  $router) {
        $this->router = $router;
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
