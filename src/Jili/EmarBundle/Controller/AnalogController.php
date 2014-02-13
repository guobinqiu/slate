<?php

namespace Jili\EmarBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

use Jili\BackendBundle\Controller\IpAuthenticatedController;

/**
 * @Route("/analog")
 */
class AnalogController extends Controller implements  IpAuthenticatedController
{

    /**
     * @Route("/cps")
     */
    public function cpsAction()
    {
        $request = $this->get('request');
        $logger= $this->get('logger');

        if( 'POST'== $request->getMethod()){

            $params = $request->request->all();

            $logger->debug('{jarod}'.__FILE__.':'.__LINE__.':'. var_export( $params, true) );

            if( !isset($params['unique_id']) || $params['unique_id'] === 'null') {
                $params['unique_id'] = $this->updateUniqueId();
            }

            if( !isset($params['create_date']) || $params['create_date'] === 'null') {
                $params['create_date']= urlencode( date('Y-m-d H:i:s') ) ;
            }

            if( !isset($params['sid']) || $params['sid'] === 'null') {
                $params['sid'] = $this->container->getParameter('emar_com.accountid');//: '458631' # sid uSer id
            }

            if( !isset($params['wid']) || $params['wid'] === 'null') {
                $params['wid'] = $this->container->getParameter('emar_com.91jili_com.websiteid');//: '708089' # wid Website id
            }

            if( !isset($params['order_time']) || $params['order_time'] === 'null') {
                $params['order_time']= urlencode( date('Y-m-d H:i:s') ) ;
            }

            if( !isset($params['prod_id']) || $params['prod_id'] === 'null') {
                $params['prod_id'] = '';
            }

            if( !isset($params['prod_name']) || $params['prod_name'] === 'null') {
                $params['prod_name'] ='';
            }

            $key =$this->container->getParameter('emar_com.91jili_com.key');
            $params['chkcode'] = strtolower(md5($params['action_id'].$params['order_no'].$params['prod_money'].$params['order_time'].$key) ) ;
            
            $logger->debug('{jarod}'.__FILE__.':'.__LINE__.':'. var_export( $params, true) );

            $sub_querystring = urldecode(http_build_query($params));

            $logger->debug('{jarod}'.__FILE__.':'.__LINE__.':'. var_export( $sub_querystring, true) );

            $sub_request_uri = $this->get('router')->getRouteCollection()->get('jili_emar_api_callback')->getPath();

            $subRequest =  Request::create($sub_request_uri,'GET', $params );
            $httpKernel = $this->container->get('http_kernel');
            $subResponse = $httpKernel->handle($subRequest, HttpKernelInterface::SUB_REQUEST);

            $sub_response_content =  $subResponse->getContent();



            $this->get('session')->getFlashBag()->add(
                'notice',
                'request: '. $sub_querystring. ' <br />'.
                'response: '. $sub_response_content 
            );

        }

        return $this->render('JiliEmarBundle:Analog:cps.html.twig' );
    }


    private function updateUniqueId() {
        $em = $this->get('doctrine')->getManager();
        $i = 0;
        do{
            $unique_id = mt_rand( 10000000,99999999);
            $o = $em->getRepository('JiliEmarBundle:EmarOrder')->findOneByOcd($unique_id ) ;
            $i++;
        } while( $i < 5 && empty( $o) );

        if( ! empty( $o)  ) {
            echo 'No unique id generated!!';
            exit;
        }

        return $unique_id;
    }
}
