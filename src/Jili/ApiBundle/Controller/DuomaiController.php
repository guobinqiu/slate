<?php

namespace Jili\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Jili\ApiBundle\Validator\Constraints\DuomaiApiOrdersPushChecksum;

/**
 * @Route("/api/duomai")
 */
class DuomaiController extends Controller
{
    /**
     * @Route("/getInfo", name="_api_duomai_getinfo");
     * @Method({"GET"});
     */
    public function getInfoAction(Request $request)
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/plain');

        $logger = $this->get('logger');

        // add request to adw_api_return?? or another table.
        $em = $this->getDoctrine()->getManager();

        // insert into duomai_api_return 
        $em->getRepository('JiliApiBundle:DuomaiApiReturn')->log( $request->getRequestUri());

        $result_validation = $this->get('duomai_request.validation')->validate($request->query );

        // II.get result_validation 
        if($result_validation['value']  === false) {
            $resp = new Response( $result_validation['code'] );
            $resp->headers->set('Content-Type', 'text/plain');
            return $resp;
        }


        // III. process.
        $result_processed  = $this->get('duomai_request.processor')
            ->process( $request->query, $result_validation['data']);
        if($result_processed['value']  === false) {
            $resp = new Response( $result_processed['code'] );
            $resp->headers->set('Content-Type', 'text/plain');
            return $resp;
        }
        
        $resp = new Response( $result_processed['code'] );
        $resp->headers->set('Content-Type', 'text/plain');
        return $resp;
    }

}
