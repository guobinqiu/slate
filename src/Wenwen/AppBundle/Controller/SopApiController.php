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

/**
 * @Route("/sop/v1_1",requirements={"_scheme"="https"})
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
     * @Route("/profile_point", name="_sop_profile_point")
     */
    public function addPointAction(Request $request)
    {
        $response = $this->preExecute();
        if ($response) {
            return $response;
        }

        $params['app_id'] = $request->request->get('app_id');
        $params['app_mid'] = $request->request->get('app_mid');
        $params['hash'] = $request->request->get('hash');
        $params['name'] = $request->request->get('name');
        $params['time'] = $request->request->get('time');
        $params['sig'] = $request->request->get('sig');

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
            //duplicated hash
            if (preg_match('/Duplicate entry/', $e->getMessage())) {
                return $this->render400Response('point already added');
            }

            $em->getConnection()->rollback();
            throw $e;
        }

        // OK
        return $this->render200Response();
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
