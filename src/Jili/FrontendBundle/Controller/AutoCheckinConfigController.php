<?php
namespace Jili\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @Route("/autoCheckIn")
 */
class AutoCheckinConfigController extends Controller {

    /**
     * @Route("/create")
     */
    public function createAction() {

        $em = $this->getDoctrine()->getManager();
        $session = $this->get('session');
        $request = $this->getRequest();

        //check login
        if (!$session->has('uid')) {
            $response = new JsonResponse();
            $response->setData(array(
                    'code' => 401,
                    'message'=> '需要登录',
                ));
            return $response;
        }

        $user_id = $session->get('uid');

        //check mothod
        if ($request->getMethod() != 'PUT' || !$request->isXmlHttpRequest()) {
            $return['code'] = 400;
            $return['message'] = "请求方法不对";
            $response = new Response(json_encode($result));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

        //check user exist
        $user = $em->getRepository('JiliApiBundle:User')->find($user_id);
        if (!$user) {
            $return['code'] = 402;
            $return['message'] = "该用户不存在";
            $response = new Response(json_encode($result));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

        //check user config auto_checkin exist
        $userConfiguration = $em->getRepository('JiliApiBundle:UserConfigurations')->findByUserId($user_id);
        if ($userConfiguration) {
            $return['code'] = 201;
            $return['message'] = "已经存在";
            $response = new Response(json_encode($result));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

        //insert db
        $userConfiguration = new UserConfigurations();
        $userConfiguration->setUser($user);
        $userConfiguration->setFlagName("auto_checkin");
        $userConfiguration->setFlagData(1);
        $em->persist($userConfiguration);
        $em->flush();

        $return['code'] = 200;
        $return['message'] = '成功';
        $response = new Response(json_encode($result));
        $response->headers->set('Content-Type', 'application/json');
        return $response;

    }

    /**
     * @Route("/delete")
     * @Template()
     */
    public function deleteAction() {
    }

    /**
     * @Route("/update")
     * @Template()
     */
    public function updateAction() {
    }

    /**
     * @Route("/get")
     * @Template()
     */
    public function getAction() {
    }

}
