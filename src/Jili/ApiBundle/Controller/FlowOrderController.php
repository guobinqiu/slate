<?php
namespace Jili\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/api/flow")
 */
class FlowOrderController extends Controller {

    /**
     * @Route("/getInfo", name="_api_flow_getinfo");
     * @Method("POST");
     */
    public function getInfoAction() {
        $logger = $this->get('logger');

        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');

        $clientIp = $request->getClientIp();

        //把接口数据写到表中flow_order_api_return
        $api_logger = $this->get('flow_order_api.init_log');
        $api_logger->log($request->getRequestUri());

        $result = file_get_contents("php://input");
        $data = json_decode($result, true);

        //对接口数据进行处理
        $service = $this->get('flow_order_request.processor');
        $service->process($data);
    }
}