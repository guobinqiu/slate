<?php

namespace Jili\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @Route("/api/offer99")
 */
class Offer99Controller extends Controller
{

    /**
     * @Route("/getInfo", name="_api_offer99_getinfo");
     * @Method({"GET"});
     */
    public function getInfoAction()
    {
        $logger = $this->get('logger');
        // add request to adw_api_return?? or another table.
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');

        $api_logger = $this->get('offer99_api.init_log');
        $api_logger->log($request->getRequestUri());
        $request_validator = $this->get('offer99_request.validation');

        $config = $this->container->getParameter('offer99_com');
        $validate_return = $request_validator->validate($request, $config);

        $result = array(
            'uid' => $request->query->get('uid', ''),
            'vcpoints'=> $request->query->get('vcpoints', ''),
            'tid'=> $request->query->get('tid', ''),
            'offer_name'=> $request->query->get('offer_name', '')
        );

        if($validate_return['valid_flag']  === false) {
            $result['status'] = 'failure';
            $result['errno'] = $validate_return['code'];

            $resp = new Response(json_encode($result)  );
            $resp->headers->set('Content-Type', 'text/plain');
            return $resp;
        }

        $offer99_service = $this->get('offer99_request.processor');
        $code_processed = $offer99_service->process( $request, $config);

        $result['status'] = 'success';
        $resp = new Response(json_encode($result)  );
        $resp->headers->set('Content-Type', 'text/plain');
        return $resp;

    }

}

