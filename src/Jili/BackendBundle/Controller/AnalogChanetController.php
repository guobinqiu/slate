<?php

namespace Jili\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * @Route("/analog/chanet",requirements={"_scheme"="https"})
 */
class AnalogChanetController extends Controller implements  IpAuthenticatedController
{
    /**
     * @Route("/cpa")
     */
    public function cpaAction()
    {

        $request = $this->get('request');
        $logger= $this->get('logger');

        if( 'POST'== $request->getMethod()) {

            $params = $request->request->all() ;

            if( $request->request->has('ocd') && strlen($request->request->get('ocd')) > 0 ) {
                $params['ocd']  =  $request->request->get('ocd') ;
            } else {
                $params['ocd']  = '99999'.rand(100,999);
            }


            $logger->debug( '{jaord}'.implode(':', array(__FILE__,__LINE__,'' )) .var_export( $params , true) );
            $sub_request_uri = $this->get('router')->getRouteCollection()->get('_api_getAdwInfo')->getPath();
            $subRequest =  Request::create($sub_request_uri,'GET', $params );
            $httpKernel = $this->container->get('http_kernel');
            $subResponse = $httpKernel->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
            $sub_response_content =  $subResponse->getContent();



            $this->get('session')->getFlashBag()->add(
                'notice',
                'request : ' . json_encode($params ) .'; response:' . $sub_response_content 
            );
        }

        return $this->render('JiliBackendBundle:AnalogChanet:cpa.html.twig');
    }

}
