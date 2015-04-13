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
     * @Method({"GET","POST"});
     */
    public function getInfoAction(Request $request)
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/plain');

        // 默认情况下 ，接口没有收到数据。直接访问的情况下 请输出 -1 或者 不输出
        if( 'GET' === $request->getMethod()) {
            return $response->setContent(-1) ;
        }


        $logger = $this->get('logger');
        // add request to adw_api_return?? or another table.
        $em = $this->getDoctrine()->getManager();

        // insert into duomai_api_return 
        
        $em->getRepository('JiliApiBundle:DuomaiApiReturn')->log( $request->getRequestUri());

        
        $result_validation = $this->get('validator')->validate($request->request );
        
        // II.get result_validation 
        if($result_validation['value']  === false) {
            $resp = new Response( $result_validation['code'] );
            $resp->headers->set('Content-Type', 'text/plain');
            return $resp;
        }

        // III. process.
        $result_processed  = $this->get('duomai_request.processor')
            ->process( $request->request );
        //    $code_processed = $offer99_service->process( $request, $config);

        //    $result['status'] = 'success';
        
        $resp = new Response(0);
        $resp->headers->set('Content-Type', 'text/plain');
        return $resp;
    }

}
