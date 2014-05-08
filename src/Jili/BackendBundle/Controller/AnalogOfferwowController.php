<?php

namespace Jili\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * @Route("/analog/offerwow",requirements={"_scheme"="https"})
 */
class AnalogOfferwowController extends Controller implements  IpAuthenticatedController
{
    /**
     * @Route("/api")
     */
    public function apiAction()
    {

        $request = $this->get('request');
        $logger= $this->get('logger');

        if( 'POST'== $request->getMethod()){
#             $logger->debug('{jarod}'.__FILE__.':'.__LINE__.':'. var_export( $request->request->all(), true) );
            $params = array(
                'memberid'=> $request->request->get('memberid'),
                'point'=> $request->request->get('point'),
                'immediate'=> $request->request->get('immediate'),
                'websiteid'=> $this->container->getParameter('offerwow_com.websiteid'),
            );

            if( $request->request->has('eventid') && strlen($request->request->get('eventid')) > 0 ) {
                $params['eventid']  =  $request->request->get('eventid') ;
            } else {
                $params['eventid']  = 'test'.substr(md5(time().__FILE__), 4);
            }

            $key =$this->container->getParameter('offerwow_com.key');
            $params['sign'] =strtoupper(md5($params['memberid'] . $params['point'] .$params['eventid'] .$params['websiteid'] .$params['immediate'] .  $key  )  );

            //todo: check memberid exits in user table.

            $sub_request_uri = $this->get('router')->getRouteCollection()->get('_api_offerwow_getinfo')->getPath();

            $subRequest =  Request::create($sub_request_uri,'GET', $params );
            $httpKernel = $this->container->get('http_kernel');
            $subResponse = $httpKernel->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
            $sub_response_content =  $subResponse->getContent();


            $this->get('session')->getFlashBag()->add(
                'notice',
                $sub_response_content 
            );

        }

        return $this->render('JiliBackendBundle:AnalogOfferwow:api.html.twig' );
    }

}
