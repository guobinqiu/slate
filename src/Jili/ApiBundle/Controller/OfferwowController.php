<?php

namespace Jili\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use Jili\ApiBundle\Entity\AdwApiReturn;

/**
 * @Route("/api/offerwow")
 */
class OfferwowController extends Controller
{

    /**
     * @Route("/getInfo", name="_api_offerwow_getinfo");
     * @Method({"GET","POST"});
     */
    public function getInfoAction()
    {
        $logger = $this->get('logger');

        // add request to adw_api_return?? or another table.
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');

        $api_logger = $this->get('offerwow_api.init_log');
        $api_logger->log($request->getRequestUri());

        $request_validator = $this->get('offerwow_request.validation');

        $config = $this->container->getParameter('offerwow_com');
        $validate_return = $request_validator->validate($request, $config);

        $result = array(
            'memberid' => $request->query->get('memberid', ''),
            'point'=> $request->query->get('point', ''),
            'websiteid'=> $request->query->get('websiteid', ''),
            'eventid'=> $request->query->get('eventid', ''),
            'immediate'=> $request->query->get('immediate', '')
        );

        if( $request->query->has('sign')) {
            $result[ 'sign'] = $request->query->get('sign');
        }

        if($validate_return[0]  === false) {
            $result['status'] = 'failure';
            $result['errno'] = $validate_return['code'];

            $resp = new Response(json_encode($result)  );
            $resp->headers->set('Content-Type', 'text/plain');
            return $resp;
        }


        $offerwow_service = $this->get('offerwow_request.processor');
        $code_processed = $offerwow_service->process( $request, $config);

        $result['status'] = 'success';
        $resp = new Response(json_encode($result)  );
        $resp->headers->set('Content-Type', 'text/plain');
        return $resp;

    }

}
