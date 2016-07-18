<?php

namespace Wenwen\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Offerwow 数据回传用Controller
 */
class OfferwowRequestController extends Controller
{
    /**
     * Offerwow 数据回传用Action
     */
    public function getInfoAction()
    {
        $request = $this->get('request');

        // 记录raw request 
        $api_logger = $this->get('offerwow_api.init_log');
        $api_logger->log($request->getRequestUri());

        // 从request中获取所有参数
        $memberid = $request->query->get('memberid');
        $point = $request->query->get('point');
        $eventid = $request->query->get('eventid');
        $websiteid = $request->query->get('websiteid');
        $immediate = $request->query->get('immediate');
        $programname = $request->query->get('programname');
        $sign = $request->query->get('sign');

        // 准备返回用的数据
        $response['memberid'] = $memberid;
        $response['point'] = $point;
        $response['websiteid'] = $websiteid;
        $response['eventid'] = $eventid;
        $response['immediate'] = $immediate;

        $offerwowRequestService = $this->get('app.offerwow_request_service');
        // 参数检查
        $result = $offerwowRequestService->validateParams($memberid, $point, $eventid, $websiteid, $immediate, $sign);
        if($result['status'] === 'failure'){
            // 参数检查不通过时，返回错误信息给offerwow
            $response['status'] = $result['status'];
            $response['errno'] = $result['errno'];
            $resp = new Response(json_encode($response));
            $resp->setStatusCode(403);
            $resp->headers->set('Content-Type', 'application/json');
            return $resp;
        }

        // 数据处理
        $result = $offerwowRequestService->processEvent($memberid, $point, $eventid, $immediate, $programname);
        if($result){
            // 内部处理完成后，返回成功信息给offerwow
            $response['status'] = 'success';
            $resp = new Response(json_encode($response));
            $resp->headers->set('Content-Type', 'application/json');
            return $resp;
        } else {
            // 内部处理失败，不返回数据给offerwow
        }
    }
}
