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
use Wenwen\FrontendBundle\Entity\CategoryType;
use Wenwen\FrontendBundle\Entity\TaskType;

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

        $app_mid = $params['app_mid'];
        $name = $params['name'];

        //check if app_mid exists.
        $em = $this->getDoctrine()->getManager();
        $sop_respondent = $em->getRepository('JiliApiBundle:SopRespondent')->retrieveById($app_mid);
        if (!$sop_respondent) {
            return $this->render400Response('invalid app_mid');
        }
        $user_id = $sop_respondent->getUserId();

        $user = $em->getRepository('JiliApiBundle:User')->find($user_id);
        if (!$user) {
            return $this->render400Response('panelist not found');
        }

        $sop_config = $this->container->getParameter('sop');
        $point_value = $sop_config['point']['profile'];

        $sig = $params['sig'];
        unset($params['sig']);

        // Verify signature
        $auth = new \SOPx\Auth\V1_1\Client($sop_config['auth']['app_id'], $sop_config['auth']['app_secret']);

        if (!$auth->verifySignature($sig, $params)) {
            return $this->render400Response('authentication failed');
        }

        // start transaction
        $em->getConnection()->beginTransaction();

        try {
            // insert sop_profile_point
            $sop_profile_point = new SopProfilePoint();
            $sop_profile_point->setUserId($user_id);
            $sop_profile_point->setName($name);
            $sop_profile_point->setHash($params['hash']);
            $sop_profile_point->setPointValue($point_value);
            $em->persist($sop_profile_point);
            $em->flush();

            // add point
            $service = $this->container->get('points_manager');
            $ad_category_id = CategoryType::SOP_EXPENSE;
            $task_type_id = TaskType::RENTENTION;
            $service->updatePoints($user_id, $point_value, $ad_category_id, $task_type_id, $name . ' 属性问卷');

            $em->getConnection()->commit();
        } catch (\Exception $e) {

            $em->getConnection()->rollback();

            $this->get('logger')->crit("Exception: ". $e->getMessage());

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
        $em = $this->getDoctrine()->getManager();
        return $this->doHandleDeliveryNotification(new SopDeliveryNotification($em));
    }

    /**
     * @Route("fulcrum/v1_1/deliveryNotificationFor91wenwen", name="fulcrum_delivery_notification_v1_1_91wenwen")
     */
    public function deliveryFulcrumDeliveryNotificationFor91wenwenAction()
    {
        $em = $this->getDoctrine()->getManager();
        return $this->doHandleDeliveryNotification(new FulcrumDeliveryNotification($em));
    }

    /**
    * Sample RequestBody - JSON
        {"time":1472695397,"app_id":"56","data":{"respondents":[{"extra_info":{"point":{"screenout":0,"quotafull":0,"complete":300}},"loi":"10","survey_id":"5567","title":"Fulcrum Survey","app_mid":"399924","quota_id":"3734"},{"extra_info":{"point":{"screenout":0,"quotafull":0,"complete":300}},"loi":"10","survey_id":"5567","title":"Fulcrum Survey","app_mid":"440690","quota_id":"3734"},{"extra_info":{"point":{"screenout":0,"quotafull":0,"complete":300}},"loi":"10","survey_id":"5567","title":"Fulcrum Survey","app_mid":"133040","quota_id":"3734"},{"extra_info":{"point":{"screenout":0,"quotafull":0,"complete":300}},"loi":"10","survey_id":"5567","title":"Fulcrum Survey","app_mid":"3769","quota_id":"3734"},{"extra_info":{"point":{"screenout":0,"quotafull":0,"complete":300}},"loi":"10","survey_id":"5567","title":"Fulcrum Survey","app_mid":"22040","quota_id":"3734"},{"extra_info":{"point":{"screenout":0,"quotafull":0,"complete":300}},"loi":"10","survey_id":"5567","title":"Fulcrum Survey","app_mid":"448241","quota_id":"3734"},{"extra_info":{"point":{"screenout":0,"quotafull":0,"complete":300}},"loi":"10","survey_id":"5567","title":"Fulcrum Survey","app_mid":"51305","quota_id":"3734"},{"extra_info":{"point":{"screenout":0,"quotafull":0,"complete":300}},"loi":"10","survey_id":"5567","title":"Fulcrum Survey","app_mid":"438470","quota_id":"3734"},{"extra_info":{"point":{"screenout":0,"quotafull":0,"complete":300}},"loi":"10","survey_id":"5567","title":"Fulcrum Survey","app_mid":"105667","quota_id":"3734"},{"extra_info":{"point":{"screenout":0,"quotafull":0,"complete":300}},"loi":"10","survey_id":"5567","title":"Fulcrum Survey","app_mid":"419","quota_id":"3734"},{"extra_info":{"point":{"screenout":0,"quotafull":0,"complete":300}},"loi":"10","survey_id":"5567","title":"Fulcrum Survey","app_mid":"177374","quota_id":"3734"},{"extra_info":{"point":{"screenout":0,"quotafull":0,"complete":300}},"loi":"10","survey_id":"5567","title":"Fulcrum Survey","app_mid":"3337","quota_id":"3734"},{"extra_info":{"point":{"screenout":0,"quotafull":0,"complete":300}},"loi":"10","survey_id":"5567","title":"Fulcrum Survey","app_mid":"438573","quota_id":"3734"},{"extra_info":{"point":{"screenout":0,"quotafull":0,"complete":300}},"loi":"10","survey_id":"5567","title":"Fulcrum Survey","app_mid":"373105","quota_id":"3734"},{"extra_info":{"point":{"screenout":0,"quotafull":0,"complete":300}},"loi":"10","survey_id":"5567","title":"Fulcrum Survey","app_mid":"444701","quota_id":"3734"},{"extra_info":{"point":{"screenout":0,"quotafull":0,"complete":300}},"loi":"10","survey_id":"5567","title":"Fulcrum Survey","app_mid":"623","quota_id":"3734"},{"extra_info":{"point":{"screenout":0,"quotafull":0,"complete":300}},"loi":"10","survey_id":"5567","title":"Fulcrum Survey","app_mid":"439493","quota_id":"3734"},{"extra_info":{"point":{"screenout":0,"quotafull":0,"complete":300}},"loi":"10","survey_id":"5567","title":"Fulcrum Survey","app_mid":"445018","quota_id":"3734"},{"extra_info":{"point":{"screenout":0,"quotafull":0,"complete":300}},"loi":"10","survey_id":"5567","title":"Fulcrum Survey","app_mid":"434980","quota_id":"3734"},{"extra_info":{"point":{"screenout":0,"quotafull":0,"complete":300}},"loi":"10","survey_id":"5567","title":"Fulcrum Survey","app_mid":"437474","quota_id":"3734"},{"extra_info":{"point":{"screenout":0,"quotafull":0,"complete":300}},"loi":"10","survey_id":"5567","title":"Fulcrum Survey","app_mid":"449047","quota_id":"3734"},{"extra_info":{"point":{"screenout":0,"quotafull":0,"complete":300}},"loi":"10","survey_id":"5567","title":"Fulcrum Survey","app_mid":"20062","quota_id":"3734"},{"extra_info":{"point":{"screenout":0,"quotafull":0,"complete":300}},"loi":"10","survey_id":"5567","title":"Fulcrum Survey","app_mid":"448853","quota_id":"3734"},{"extra_info":{"point":{"screenout":0,"quotafull":0,"complete":300}},"loi":"10","survey_id":"5567","title":"Fulcrum Survey","app_mid":"438049","quota_id":"3734"},{"extra_info":{"point":{"screenout":0,"quotafull":0,"complete":300}},"loi":"10","survey_id":"5567","title":"Fulcrum Survey","app_mid":"19378","quota_id":"3734"},{"extra_info":{"point":{"screenout":0,"quotafull":0,"complete":300}},"loi":"10","survey_id":"5567","title":"Fulcrum Survey","app_mid":"428061","quota_id":"3734"},{"extra_info":{"point":{"screenout":0,"quotafull":0,"complete":300}},"loi":"10","survey_id":"5567","title":"Fulcrum Survey","app_mid":"417403","quota_id":"3734"},{"extra_info":{"point":{"screenout":0,"quotafull":0,"complete":300}},"loi":"10","survey_id":"5567","title":"Fulcrum Survey","app_mid":"288066","quota_id":"3734"},{"extra_info":{"point":{"screenout":0,"quotafull":0,"complete":300}},"loi":"10","survey_id":"5567","title":"Fulcrum Survey","app_mid":"11933","quota_id":"3734"},{"extra_info":{"point":{"screenout":0,"quotafull":0,"complete":300}},"loi":"10","survey_id":"5567","title":"Fulcrum Survey","app_mid":"363509","quota_id":"3734"},{"extra_info":{"point":{"screenout":0,"quotafull":0,"complete":300}},"loi":"10","survey_id":"5567","title":"Fulcrum Survey","app_mid":"223407","quota_id":"3734"},{"extra_info":{"point":{"screenout":0,"quotafull":0,"complete":300}},"loi":"10","survey_id":"5567","title":"Fulcrum Survey","app_mid":"285201","quota_id":"3734"},{"extra_info":{"point":{"screenout":0,"quotafull":0,"complete":300}},"loi":"10","survey_id":"5567","title":"Fulcrum Survey","app_mid":"223351","quota_id":"3734"},{"extra_info":{"point":{"screenout":0,"quotafull":0,"complete":300}},"loi":"10","survey_id":"5567","title":"Fulcrum Survey","app_mid":"60746","quota_id":"3734"}]}}
    * Sample RequestBody - array
        array(
        'time' => 1472695397,
        'app_id' => 56,
        'data' => array(
            'respondents' => array(
                'extra_info' => array(
                    'point' => array(
                        'screenout' => 0,
                        'quotafull' => 0,
                        'complete' => 300
                    ),
                'loi' => 10,
                'survey_id' => 5567,
                'title' => 'Fulcrum Survey',
                'app_mid' => 399924,
                'quota_id' => 3734
                )
            'respondents' => array(
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

    */


    public function doHandleDeliveryNotification(DeliveryNotification $notification)
    {
        $request = $this->get('request');

        $request_body = $this->getRequestBody();
        $request_data = $request_body ? json_decode($request_body, true) : array ();

        // Verify signature
        $sop_config = $this->container->getParameter('sop');
        $auth = new \SOPx\Auth\V1_1\Client($sop_config['auth']['app_id'], $sop_config['auth']['app_secret']);
        $sig = $request->headers->get('X-Sop-Sig');

        $this->container->get('logger')->info('----------------'.__METHOD__.' sig='.$sig);
        $this->container->get('logger')->info('----------------'.__METHOD__.' request_body='.$request_body);

        if (!$auth->verifySignature($sig, $request_body)) {
            $this->container->get('logger')->error('----------------'.__METHOD__.'403');
            return $this->render403Response('authentication failed');
        }

        if (!isset($request_data['data']) || !isset($request_data['data']['respondents'])) {
            return $this->render400Response('data.respondents not found!');
        }

        $unsubscribed_app_mids = $notification->send($request_data['data']['respondents']);

        $res = array (
            'meta' => array (
                'code' => 200,
                'message' => ''
            )
        );
        if (sizeof($unsubscribed_app_mids)) {
            $res['data'] = array (
                'respondents-not-found' => $unsubscribed_app_mids
            );
        }
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
