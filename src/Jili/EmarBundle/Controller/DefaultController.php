<?php

namespace Jili\EmarBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * @Route("/default", requirements={"_scheme"="http"})
 */
class DefaultController extends Controller
{
    /**
     * @Route("/redirect")
     */
    public function redirectAction()
    {
        $request = $this->get('request');
        $logger = $this->get('logger');
        $session = $request->getSession();
        $url = $request->get('m');
#         $logger->debug('{jarod}'.implode(':', array(__FILE__,__LINE__,'url', '' )). var_export($url, true));
        // check login
        if($session->has('uid')){
            if( false !== strpos($url, 'APIMemberId') ) {
                $url = str_replace('APIMemberId', $session->get('uid') , $url );
#         $logger->debug('{jarod}'.implode(':', array(__FILE__,__LINE__,'url', '' )). var_export($url, true));
            }  else if( 1===preg_match("/&e=(\d+)/i", $url,$m) && count($m) === 2 && $m[1] !== $session->get('uid') ) {
                $url = str_replace($m[0], '&e='.$session->get('uid') , $url );
#         $logger->debug('{jarod}'.implode(':', array(__FILE__,__LINE__,'url', '' )). var_export($url, true));
            } else {
                return $this->redirect($this->generateUrl('_homepage') );
            }
            return $this->redirect( $url, 302);
        } else {
            $session->set('referer', $url);
            return $this->forward( 'JiliApiBundle:User:login' );
        }
        // set session
        // redirect to login
        // forward to the pages?
//        return $this->render('JiliEmarBundle:Default:index.html.twig', array('name' => $name));
    }
}
