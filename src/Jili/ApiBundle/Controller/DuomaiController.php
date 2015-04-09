<?php

namespace Jili\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("/api/duomai")
 */
class DuomaiController extends Controller
{
    /**
     * @Route("/getInfo", name="_api_duomai_getinfo");
     * @Method({"GET"});
     */
    public function getInfoAction()
    {
        $logger = $this->get('logger');
        // add request to adw_api_return?? or another table.
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
// insert into duomai_api_return 
        // $api_logger->log($request->getRequestUri());

        // $request_validator = $this->get('offer99_request.validation');
// write a validation service !!!
        
        $config = $this->container->getParameter('duomai__com');
//        $validate_return = $request_validator->validate($request, $config);

        // get result_validation 
//        $result = array(
//            'uid' => $request->query->get('uid', ''),
//            'vcpoints'=> $request->query->get('vcpoints', ''),
//            'tid'=> $request->query->get('tid', ''),
//            'offer_name'=> $request->query->get('offer_name', '')
//        );
//
//        if($validate_return['valid_flag']  === false) {
//            $result['status'] = 'failure';
//            $result['errno'] = $validate_return['code'];
//
//            $resp = new Response(json_encode($result)  );
//            $resp->headers->set('Content-Type', 'text/plain');
//            return $resp;
//        }

        $offer99_service = $this->get('offer99_request.processor');
        $code_processed = $offer99_service->process( $request, $config);

        $result['status'] = 'success';
        $resp = new Response(json_encode($result)  );
        $resp->headers->set('Content-Type', 'text/plain');
        return $resp;

    }

}
