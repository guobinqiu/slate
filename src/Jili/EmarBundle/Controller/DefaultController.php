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
#        $logger->debug('{jarod}'.implode(':', array(__FILE__,__LINE__,'url', '' )). var_export($url, true));

        // check login
        if( $session->has('uid') ){
            $url = str_replace('APIMemberId', $session->get('uid') , $url );
            return $this->redirect( $url, 302);
        } else if( false !== strpos('APIMemberId', $url)  && 1 ===  preg_match('/e=(\d+)/', $url, $m)  ) {
            if( count($m) === 2 && is_numeric($m[1]) ) {
                $uid = $m[1];
                $em = $this->getDoctrine()->getManager();
                $user = $em->getRepository('JiliApiBundle:User')->find($uid);
                if($user) {
                    return $this->redirect( $url, 302);
                }
            }
        }    

        if( false !== strpos('APIMemberId', $url)){
            $session->set('referer', $url);
            return $this->forward( 'JiliApiBundle:User:login' );
        } else {
            return $this->forward( 'JiliApiBundle:Top:index' );
        }

        // set session
        // redirect to login
        // forward to the pages?
//        return $this->render('JiliEmarBundle:Default:index.html.twig', array('name' => $name));
    }
}
