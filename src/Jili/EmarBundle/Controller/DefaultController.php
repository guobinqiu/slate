<?php

namespace Jili\EmarBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * @Route("/default", requirements={"_scheme": "http"})
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
            str_replace('APIMemberId', $session->get('uid') , $url );
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
