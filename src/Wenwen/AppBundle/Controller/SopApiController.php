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
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Wenwen\AppBundle\Entity\SopProfilePoint;
use Jili\ApiBundle\Entity\AdCategory;
use Jili\ApiBundle\Entity\TaskHistory00;
use Wenwen\AppBundle\WebService\Sop\SopDeliveryNotificationHandler;

/**
 * @Route("/",requirements={"_scheme"="https"})
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
            $ad_category_id = AdCategory::ID_QUESTIONNAIRE_EXPENSE;
            $task_type_id = TaskHistory00::TASK_TYPE_SURVEY;
            $service->updatePoints($user_id, $point_value, $ad_category_id, $task_type_id, $name . ' 属性问卷');

            $em->getConnection()->commit();
        } catch (\Exception $e) {

            $em->getConnection()->rollback();

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
    public function deliveryNotificationFor91wenwenAction(Request $request)
    {
        return $this->doHandleDeliveryNotification(SopDeliveryNotificationHandler::TYPE_SOP);
    }

    /**
     * @Route("fulcrum/v1_1/deliveryNotificationFor91wenwen", name="fulcrum_delivery_notification_v1_1_91wenwen")
     */
    public function deliveryFulcrumDeliveryNotificationFor91wenwenAction(Request $request)
    {
        return $this->doHandleDeliveryNotification(SopDeliveryNotificationHandler::TYPE_FULCRUM);
    }

    public function doHandleDeliveryNotification($type)
    {
        $request = $this->get('request');

        $request_body = $this->getRequestBody();
        $request_data = $request_body ? json_decode($request_body, true) : array ();

        // Verify signature
        $sop_config = $this->container->getParameter('sop');
        $auth = new \SOPx\Auth\V1_1\Client($sop_config['auth']['app_id'], $sop_config['auth']['app_secret']);
        $sig = $request->headers->get('X-Sop-Sig');

        if (!$auth->verifySignature($sig, $request_body)) {
            return $this->render403Response('authentication failed');
        }

        if (!isset($request_data['data']) || !isset($request_data['data']['respondents'])) {
            return $this->render400Response('data.respondents not found!');
        }

        $handler = new SopDeliveryNotificationHandler($request_data['data']['respondents'], $type);
        $handler->setUpRespondentsToMail();
        $handler->sendMailingToRespondents();

        $unsubscribed_app_mids = $handler->getUnsubscribedAppMids();
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
    public function getRequestBody()
    {
        return $this->get('request')->get('request_body') ? $this->get('request')->get('request_body') : file_get_contents('php://input');
    }

    public function render200Response()
    {
        $body_array = array (
            'meta' => array (
                'code' => 200
            )
        );
        return $this->renderJSONResponse($body_array);
    }

    public function render400Response($error)
    {
        $body_array = array (
            'meta' => array (
                'code' => 400,
                'message' => $error
            )
        );
        return $this->renderJSONResponse($body_array, 400);
    }

    public function render403Response($error)
    {
        $body_array = array (
            'meta' => array (
                'code' => 403,
                'message' => $error
            )
        );
        return $this->renderJSONResponse($body_array, 403);
    }

    public function renderJSONResponse($body_array, $status_code = 200)
    {
        $response = new JsonResponse($body_array, $status_code);
        return $response;
    }
}
