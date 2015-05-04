<?php

namespace Jili\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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

        // add request to adw_api_return?? or another table.
        $em = $this->getDoctrine()->getManager();

        //I. insert into duomai_api_return 
        $em->getRepository('JiliApiBundle:DuomaiApiReturn')->log( $request->getRequestUri());
        $result_validation = $this->get('duomai_request.validation')->validate($request->query );

        // II.get result_validation 
        if($result_validation['value']  === false) {
            $response->setContent($result_validation['code'] );
            return $response;
        }

        // III. process.
        $result_processed  = $this->get('duomai_request.processor')
            ->process( $request->query, $result_validation['data']);
        if($result_processed['value']  === false) {
            $response->setContent($result_validation['code'] );
            return $response;
        }
        
        $response->setContent($result_validation['code'] );
        return $response;
    }

}
