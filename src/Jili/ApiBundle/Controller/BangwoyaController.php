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
 * @Route("/api/bangwoya")
 */
class BangwoyaController extends Controller {

    /**
     * @Route("/getInfo", name="_api_bangwoya_getinfo");
     * @Method({"GET","POST"});
     */
    public function getInfoAction() {
        $logger = $this->get('logger');

        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');

        $clientIp = $request->getClientIp();

        //把接口数据写到表中bangwoya_api_return
        $api_logger = $this->get('bangwoya_api.init_log');
        $api_logger->log($request->getRequestUri());

        //partnerid 合作客户的玩家（用户）ID
        //vmoney 虚拟货币的数量
        //tid 用户(玩家)在天芒云参与的任务ID
        //nonceStr 密码验证(32位小写字符串)
        $tid = $request->query->get('tid', '');
        $partnerid = $request->query->get('partnerid', '');
        $vmoney = $request->query->get('vmoney', '');
        $nonceStr = $request->query->get('nonceStr', '');

        //验证接口数据
        $request_validator = $this->get('bangwoya_request.validation');
        $validate_return = $request_validator->validate($tid, $partnerid, $vmoney, $nonceStr, $clientIp);

        $result = array (
            'partnerid' => $request->query->get('partnerid', ''),
            'vmoney' => $request->query->get('vmoney', ''),
            'tid' => $request->query->get('tid', '')
        );

        //验证不通过返回
        if ($validate_return['valid_flag'] === false) {
            $result['status'] = 'no';
            $result['errno'] = $validate_return['code'];

            $resp = new Response(json_encode($result));
            $resp->headers->set('Content-Type', 'text/plain');
            return $resp;
        }

        //对接口数据进行处理
        $bangwoya_service = $this->get('bangwoya_request.processor');
        $order_id = $bangwoya_service->process($tid, $partnerid, $vmoney);

        //成功
        $result['status'] = 'success';
        $result['sn'] = $order_id;

        //返回状态给对方
        $resp = new Response(json_encode($result));
        $resp->headers->set('Content-Type', 'text/plain');
        return $resp;
    }
}