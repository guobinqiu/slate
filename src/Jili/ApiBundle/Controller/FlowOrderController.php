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
 * @Route("/api/flow")
 */
class FlowOrderController extends Controller {

    /**
     * @Route("/getInfo", name="_api_flow_getinfo");
     * @Method("POST");
     */
    public function getInfoAction(Request $request) {
        $result = $request->getContent();
        $data = json_decode($result, true);

        $data['client_ip'] = $request->getClientIp();

        //把接口数据写到表中flow_order_api_return
        $api_logger = $this->get('flow_order_api.init_log');
        $api_logger->log($result);

        //对接口数据进行处理
        $service = $this->get('flow_order_request.processor');
        $result = $service->process($data);

        $resp = new Response(json_encode($result));
        $resp->headers->set('Content-Type', 'application/json');
        return $resp;
    }
}
