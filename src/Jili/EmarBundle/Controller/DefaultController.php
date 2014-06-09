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

        //
        if( $session->has('uid') ){
            if( false !== strpos( $url,'APIMemberId')){
                $url = str_replace('APIMemberId', $session->get('uid') , $url );
            } else if( 1 !==  preg_match('/e=(\d+)/', $url, $m) &&  1 !== preg_match( '/\|(\d+)$/', $url, $m) ) {

                $url = $this->get('router')->generate( '_homepage',array(),true) ;
            }
            return $this->redirect( $url, 302);
        } else if( false === strpos($url,'APIMemberId' )  ){
            $parsed =  parse_url($url);
            if( 1 ===  preg_match('/e=(\d+)/', $url, $m)  ) {
                if( count($m) === 2 && is_numeric($m[1]) ) {
                    $uid = $m[1];
                    $em = $this->getDoctrine()->getManager();
                    $user = $em->getRepository('JiliApiBundle:User')->find($uid);
                    if($user) {
                        return $this->redirect( $url, 302);
                    }
                }
            } else if( isset($parsed['host']) && $parsed['host'] === 'www.amazon.cn' && 1 === preg_match( '/\|(\d+)$/', $url, $m) ) {

                if( count($m) === 2 && is_numeric($m[1]) ) {
                    $uid = $m[1];
                    $em = $this->getDoctrine()->getManager();
                    $user = $em->getRepository('JiliApiBundle:User')->find($uid);

                    if($user) {
                        return $this->redirect( $url, 302);
                    }

                } 
            }     

        }

        if( false !== strpos( $url,'APIMemberId')){
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
