<?php

namespace Wenwen\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Wenwen\AppBundle\Utility\SopValidator;
use Wenwen\AppBundle\Entity\SopProfilePoint;
use Wenwen\FrontendBundle\ServiceDependency\Notification\DeliveryNotification;
use Wenwen\FrontendBundle\ServiceDependency\Notification\FulcrumDeliveryNotification;
use Wenwen\FrontendBundle\ServiceDependency\Notification\SopDeliveryNotification;
use Wenwen\FrontendBundle\Model\CategoryType;
use Wenwen\FrontendBundle\Model\TaskType;

/**
 * @Route("/")
 */
class SopApiController extends Controller
{
    private static $point_comment = '%s 属性问卷';

    public function preExecute()
    {
        if ($this->get('request')->getMethod() !== 'POST') {
            $response = $this->renderJSONResponse(array (
                'meta' => array (
                    'code' => 405,
                    'message' => 'method not allowed'
                )
            ), 405);
            return $response;
        }
        return false;
    }

    /**
     * @Route("sop/v1_1/profile_point", name="_sop_profile_point")
     */
    public function addPointAction(Request $request)
    {
        $response = $this->preExecute();
        if ($response) {
            return $response;
        }

        $params = $request->request->all();

        // validate required request param
        $validator = new SopValidator($params);

        if (!$validator->validate()) {
            return $this->render400Response($validator->getErrors());
        }

        $appMid = $params['app_mid'];
        $name = $params['name'];

        //check if app_mid exists.
        $em = $this->getDoctrine()->getManager();
        $sopRespondent = $em->getRepository('JiliApiBundle:SopRespondent')->retrieveByAppMid($appMid);
        if (!$sopRespondent) {
            return $this->render400Response('invalid app_mid');
        }
        $userId = $sopRespondent->getUserId();

        $user = $em->getRepository('WenwenFrontendBundle:User')->find($userId);
        if (!$user) {
            return $this->render400Response('panelist was not found');
        }

        $sopConfig = $this->container->getParameter('sop');
        $pointValue = $sopConfig['point']['profile'];

        $sig = $params['sig'];
        unset($params['sig']);

        // Verify signature
        $appId = $params['app_id'];
        $appSecret = $this->container->get('app.user_service')->getAppSecretByAppId($appId);
        $auth = new \SOPx\Auth\V1_1\Client($appId, $appSecret);

        $result = $auth->verifySignature($sig, $params);

        if (!$result['status']) {
            $this->container->get('logger')->error(__METHOD__ . ' errMsg='.$result['msg']);
            $this->container->get('logger')->error(__METHOD__ . ' sig='.$sig);
            $this->container->get('logger')->error(__METHOD__ . ' request_body='.$this->getRequestBody());
            return $this->render400Response('authentication failed');
        }

        // start transaction
        $em->getConnection()->beginTransaction();

        try {
            // insert sop_profile_point
            $sopProfilePoint = new SopProfilePoint();
            $sopProfilePoint->setUserId($userId);
            $sopProfilePoint->setName($name);
            $sopProfilePoint->setHash($params['hash']);
            $sopProfilePoint->setPointValue($pointValue);
            $em->persist($sopProfilePoint);
            $em->flush();

            // add point
            $this->get('app.point_service')->addPoints(
                $user,
                $pointValue,
                CategoryType::SOP_EXPENSE,
                TaskType::RENTENTION,
                $name . ' 属性问卷',
                $sopProfilePoint
            );

            $em->getConnection()->commit();
        } catch (\Exception $e) {

            $em->getConnection()->rollback();

            $this->get('logger')->error(__METHOD__ . $e->getMessage());

            //duplicated hash
            if (preg_match('/Duplicate entry/', $e->getMessage())) {
                return $this->render400Response('point already added');
            }

            throw $e;
        }

        // OK
        return $this->render200Response();
    }

    /**
     * @Route("sop/v1_1/deliveryNotificationFor91wenwen", name="sop_delivery_notification_v1_1_91wenwen")
     */
    public function deliveryNotificationFor91wenwenAction()
    {
        $this->get('monolog.logger.sop_notification')->info("callback sop delivery notification");
        return $this->doHandleDeliveryNotification(
            new SopDeliveryNotification(
                $this->getDoctrine()->getManager(),
                $this->get('app.survey_sop_service')
            )
        );
    }

    /**
     * @Route("fulcrum/v1_1/deliveryNotificationFor91wenwen", name="fulcrum_delivery_notification_v1_1_91wenwen")
     */
    public function deliveryFulcrumDeliveryNotificationFor91wenwenAction()
    {
        $this->get('monolog.logger.sop_notification')->info("callback fulcrum delivery notification");
        return $this->doHandleDeliveryNotification(
            new FulcrumDeliveryNotification(
                $this->getDoctrine()->getManager(),
                $this->get('app.survey_fulcrum_service')
            )
        );
    }

    /**
    * return a dummy request body(json) from SOP delivery notification
    */
    public function dummyRequestBody(){

        $requestBody = '{"time":1472695397,"app_id":"56","data":{"respondents":[{"extra_info":{"point":{"screenout":0,"quotafull":0,"complete":300}},"loi":"10","survey_id":"5567","title":"Fulcrum Survey","app_mid":"399924","quota_id":"3734"},{"extra_info":{"point":{"screenout":0,"quotafull":0,"complete":300}},"loi":"10","survey_id":"5567","title":"Fulcrum Survey","app_mid":"440690","quota_id":"3734"},{"extra_info":{"point":{"screenout":0,"quotafull":0,"complete":300}},"loi":"10","survey_id":"5567","title":"Fulcrum Survey","app_mid":"133040","quota_id":"3734"},{"extra_info":{"point":{"screenout":0,"quotafull":0,"complete":300}},"loi":"10","survey_id":"5567","title":"Fulcrum Survey","app_mid":"3769","quota_id":"3734"},{"extra_info":{"point":{"screenout":0,"quotafull":0,"complete":300}},"loi":"10","survey_id":"5567","title":"Fulcrum Survey","app_mid":"22040","quota_id":"3734"},{"extra_info":{"point":{"screenout":0,"quotafull":0,"complete":300}},"loi":"10","survey_id":"5567","title":"Fulcrum Survey","app_mid":"448241","quota_id":"3734"},{"extra_info":{"point":{"screenout":0,"quotafull":0,"complete":300}},"loi":"10","survey_id":"5567","title":"Fulcrum Survey","app_mid":"51305","quota_id":"3734"},{"extra_info":{"point":{"screenout":0,"quotafull":0,"complete":300}},"loi":"10","survey_id":"5567","title":"Fulcrum Survey","app_mid":"438470","quota_id":"3734"},{"extra_info":{"point":{"screenout":0,"quotafull":0,"complete":300}},"loi":"10","survey_id":"5567","title":"Fulcrum Survey","app_mid":"105667","quota_id":"3734"},{"extra_info":{"point":{"screenout":0,"quotafull":0,"complete":300}},"loi":"10","survey_id":"5567","title":"Fulcrum Survey","app_mid":"419","quota_id":"3734"},{"extra_info":{"point":{"screenout":0,"quotafull":0,"complete":300}},"loi":"10","survey_id":"5567","title":"Fulcrum Survey","app_mid":"177374","quota_id":"3734"},{"extra_info":{"point":{"screenout":0,"quotafull":0,"complete":300}},"loi":"10","survey_id":"5567","title":"Fulcrum Survey","app_mid":"3337","quota_id":"3734"},{"extra_info":{"point":{"screenout":0,"quotafull":0,"complete":300}},"loi":"10","survey_id":"5567","title":"Fulcrum Survey","app_mid":"438573","quota_id":"3734"},{"extra_info":{"point":{"screenout":0,"quotafull":0,"complete":300}},"loi":"10","survey_id":"5567","title":"Fulcrum Survey","app_mid":"373105","quota_id":"3734"},{"extra_info":{"point":{"screenout":0,"quotafull":0,"complete":300}},"loi":"10","survey_id":"5567","title":"Fulcrum Survey","app_mid":"444701","quota_id":"3734"},{"extra_info":{"point":{"screenout":0,"quotafull":0,"complete":300}},"loi":"10","survey_id":"5567","title":"Fulcrum Survey","app_mid":"623","quota_id":"3734"},{"extra_info":{"point":{"screenout":0,"quotafull":0,"complete":300}},"loi":"10","survey_id":"5567","title":"Fulcrum Survey","app_mid":"439493","quota_id":"3734"},{"extra_info":{"point":{"screenout":0,"quotafull":0,"complete":300}},"loi":"10","survey_id":"5567","title":"Fulcrum Survey","app_mid":"445018","quota_id":"3734"},{"extra_info":{"point":{"screenout":0,"quotafull":0,"complete":300}},"loi":"10","survey_id":"5567","title":"Fulcrum Survey","app_mid":"434980","quota_id":"3734"},{"extra_info":{"point":{"screenout":0,"quotafull":0,"complete":300}},"loi":"10","survey_id":"5567","title":"Fulcrum Survey","app_mid":"437474","quota_id":"3734"},{"extra_info":{"point":{"screenout":0,"quotafull":0,"complete":300}},"loi":"10","survey_id":"5567","title":"Fulcrum Survey","app_mid":"449047","quota_id":"3734"},{"extra_info":{"point":{"screenout":0,"quotafull":0,"complete":300}},"loi":"10","survey_id":"5567","title":"Fulcrum Survey","app_mid":"20062","quota_id":"3734"},{"extra_info":{"point":{"screenout":0,"quotafull":0,"complete":300}},"loi":"10","survey_id":"5567","title":"Fulcrum Survey","app_mid":"448853","quota_id":"3734"},{"extra_info":{"point":{"screenout":0,"quotafull":0,"complete":300}},"loi":"10","survey_id":"5567","title":"Fulcrum Survey","app_mid":"438049","quota_id":"3734"},{"extra_info":{"point":{"screenout":0,"quotafull":0,"complete":300}},"loi":"10","survey_id":"5567","title":"Fulcrum Survey","app_mid":"19378","quota_id":"3734"},{"extra_info":{"point":{"screenout":0,"quotafull":0,"complete":300}},"loi":"10","survey_id":"5567","title":"Fulcrum Survey","app_mid":"428061","quota_id":"3734"},{"extra_info":{"point":{"screenout":0,"quotafull":0,"complete":300}},"loi":"10","survey_id":"5567","title":"Fulcrum Survey","app_mid":"417403","quota_id":"3734"},{"extra_info":{"point":{"screenout":0,"quotafull":0,"complete":300}},"loi":"10","survey_id":"5567","title":"Fulcrum Survey","app_mid":"288066","quota_id":"3734"},{"extra_info":{"point":{"screenout":0,"quotafull":0,"complete":300}},"loi":"10","survey_id":"5567","title":"Fulcrum Survey","app_mid":"11933","quota_id":"3734"},{"extra_info":{"point":{"screenout":0,"quotafull":0,"complete":300}},"loi":"10","survey_id":"5567","title":"Fulcrum Survey","app_mid":"363509","quota_id":"3734"},{"extra_info":{"point":{"screenout":0,"quotafull":0,"complete":300}},"loi":"10","survey_id":"5567","title":"Fulcrum Survey","app_mid":"223407","quota_id":"3734"},{"extra_info":{"point":{"screenout":0,"quotafull":0,"complete":300}},"loi":"10","survey_id":"5567","title":"Fulcrum Survey","app_mid":"285201","quota_id":"3734"},{"extra_info":{"point":{"screenout":0,"quotafull":0,"complete":300}},"loi":"10","survey_id":"5567","title":"Fulcrum Survey","app_mid":"223351","quota_id":"3734"},{"extra_info":{"point":{"screenout":0,"quotafull":0,"complete":300}},"loi":"10","survey_id":"5567","title":"Fulcrum Survey","app_mid":"60746","quota_id":"3734"}]}}';
        return $requestBody;
    }


    /**
    * return a dummy request data from SOP delivery notification
    */
    public function dummyRequestData(){

        $requestData = array(
            'time' => 1472695397,
            'app_id' => 56,
            'data' => array(
                'respondents' => array(
                    array(
                        'extra_info' => array(
                            'point' => array(
                                'screenout' => 0,
                                'quotafull' => 0,
                                'complete' => 300
                                )
                            ),
                        'loi' => 10,
                        'survey_id' => 5567,
                        'title' => 'Fulcrum Survey',
                        'app_mid' => 399924,
                        'quota_id' => 3734
                        ),
                    array(
                        'extra_info' => array(
                            'point' => array(
                                'screenout' => 0,
                                'quotafull' => 0,
                                'complete' => 300
                                )
                            ),
                        'loi' => 10,
                        'survey_id' => 5567,
                        'title' => 'Fulcrum Survey',
                        'app_mid' => 399924,
                        'quota_id' => 3734
                        )
                )
            )
        );
        return $requestData;
    }

    public function doHandleDeliveryNotification(DeliveryNotification $notification)
    {
        $request = $this->get('request');

        $request_body = $this->getRequestBody();
        $request_data = $request_body ? json_decode($request_body, true) : array ();

        // Record notification infos.
        if(isset($request_data['data']) && isset($request_data['data']['respondents'])){
            foreach($request_data['data']['respondents'] as $respondent){
                $this->get('monolog.logger.sop_notification')->info(json_encode($respondent));
            }
        }

        // Verify signature
        $appId = $request_data['app_id'];
        $appSecret = $this->container->get('app.user_service')->getAppSecretByAppId($appId);
        $auth = new \SOPx\Auth\V1_1\Client($appId, $appSecret);
        $sig = $request->headers->get('X-Sop-Sig');

        $result = $auth->verifySignature($sig, $request_body);

        if (!$result['status']) {
            $this->container->get('logger')->error(__METHOD__ . ' errMsg='.$result['msg']);
            $this->container->get('logger')->error(__METHOD__ . ' sig='.$sig);
            $this->container->get('logger')->error(__METHOD__ . ' request_body='.$request_body);
            return $this->render403Response('authentication failed');
        }

        if (!isset($request_data['data']) || !isset($request_data['data']['respondents'])) {
            return $this->render400Response('data.respondents was not found!');
        }

        $this->get('monolog.logger.sop_notification')->info('Start notification');
        //$unsubscribed_app_mids = $notification->send($request_data['data']['respondents']);
        $notification->send($request_data['data']['respondents']);
        $this->get('monolog.logger.sop_notification')->info('End notification');

        $res = array (
            'meta' => array (
                'code' => 200,
                'message' => ''
            )
        );

//        禁掉sop端屏蔽用户功能，因为它造成多次事故（用户收不到问卷）
//        if (sizeof($unsubscribed_app_mids)) {
//            $res['data'] = array (
//                'respondents-not-found' => $unsubscribed_app_mids
//            );
//        }
        return $this->renderJsonResponse($res);
    }

    # To test request body
    private function getRequestBody()
    {
        return $this->get('request')->get('request_body') ? $this->get('request')->get('request_body') : file_get_contents('php://input');
    }

    private function render200Response()
    {
        $body_array = array (
            'meta' => array (
                'code' => 200
            )
        );
        return $this->renderJSONResponse($body_array);
    }

    private function render400Response($error)
    {
        $body_array = array (
            'meta' => array (
                'code' => 400,
                'message' => $error
            )
        );
        return $this->renderJSONResponse($body_array, 400);
    }

    private function render403Response($error)
    {
        $body_array = array (
            'meta' => array (
                'code' => 403,
                'message' => $error
            )
        );
        return $this->renderJSONResponse($body_array, 403);
    }

    private function renderJSONResponse($body_array, $status_code = 200)
    {
        $response = new JsonResponse($body_array, $status_code);
        return $response;
    }
}
