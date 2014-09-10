<?php
namespace Jili\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use Jili\ApiBundle\Entity\UserConfigurations;

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
        $request = $this->get('request');

        //check login
        if (!$session->has('uid')) {
            $return['code'] = 401;
            $return['message'] = "需要登录";
            $response = new JsonResponse();
            $response->setData($return);
            return $response;
        }
        $user_id = $session->get('uid');

        //check mothod
        if ($request->getMethod() != 'PUT' || !$request->isXmlHttpRequest()) {
            $return['code'] = 400;
            $return['message'] = "请求方法不对";
            $response = new JsonResponse();
            $response->setData($return);
            return $response;
        }

        //check user config auto_checkin exist
        $user = $em->getRepository('JiliApiBundle:User')->find($user_id);
        $userConfiguration = $em->getRepository('JiliApiBundle:UserConfigurations')->searchUserConfiguration("auto_checkin", $user);
        if ($userConfiguration) {
            $return['code'] = 201;
            $return['message'] = "已经存在";
            $response = new JsonResponse();
            $response->setData($return);
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
        $response = new JsonResponse();
        $response->setData($return);
        return $response;

    }

    /**
     * @Route("/delete")
     */
    public function deleteAction() {
        $em = $this->getDoctrine()->getManager();
        $session = $this->get('session');
        $request = $this->get('request');

        //check login
        if (!$session->has('uid')) {
            $return['code'] = 401;
            $return['message'] = "需要登录";

            $response = new JsonResponse();
            $response->setData($return);
            return $response;
        }
        $user_id = $session->get('uid');

        //check mothod
        if ($request->getMethod() != 'DELETE' || !$request->isXmlHttpRequest()) {
            $return['code'] = 400;
            $return['message'] = "请求方法不对";
            $response = new JsonResponse();
            $response->setData($return);
            return $response;
        }

        //check user config auto_checkin exist
        $user = $em->getRepository('JiliApiBundle:User')->find($user_id);
        $userConfiguration = $em->getRepository('JiliApiBundle:UserConfigurations')->searchUserConfiguration("auto_checkin", $user);
        if ($userConfiguration) {
            $return['code'] = 404;
            $return['message'] = "记录不存在";
            $response = new JsonResponse();
            $response->setData($return);
            return $response;
        }

        //update user config
        $userConfiguration[0]->setFlagData(0);
        $em->persist($userConfiguration[0]);
        $em->flush();
        $return['code'] = 200;
        $return['data']['countOfRemoved'] = "完成";
        $return['message'] = "完成";
        $response = new JsonResponse();
        $response->setData($return);
        return $response;

        //    try {
        //    transaction start
        //    delete from user_configurations where user_id  = uid and flag_name ='auto_checkin'
        //    or
        //    update user_configuration set flag_data= false where  user_id  = uid and flag_name ='auto_checkin'
        //    transaction commit
        //    } catch() {
        //    transaction roll back
        //    return json:   code: 500, message: 出错了。
        //    }
        //    return json code 200, data: countOfRemoved: 1, message: 完成
    }

    /**
     * @Route("/update")
     * @Template()
     */
    public function updateAction() {
        $em = $this->getDoctrine()->getManager();
        $session = $this->get('session');
        $request = $this->get('request');

        //check login
        if (!$session->has('uid')) {
            $return['code'] = 401;
            $return['message'] = "需要登录";

            $response = new JsonResponse();
            $response->setData($return);
            return $response;
        }
        $user_id = $session->get('uid');

        //check mothod
        if ($request->getMethod() != 'GET' || !$request->isXmlHttpRequest()) {
            $return['code'] = 400;
            $return['message'] = "请求方法不对";
            $response = new JsonResponse();
            $response->setData($return);
            return $response;
        }

        //check user config auto_checkin exist
        $user = $em->getRepository('JiliApiBundle:User')->find($user_id);
        $userConfiguration = $em->getRepository('JiliApiBundle:UserConfigurations')->searchUserConfiguration("auto_checkin", $user);
        if ($userConfiguration) {
            $return['code'] = 404;
            $return['message'] = "记录不存在";
            $response = new JsonResponse();
            $response->setData($return);
            return $response;
        }

        //update user config
        $userConfiguration[0]->setFlagData(1); //应该根据传值吧？
        $em->persist($userConfiguration[0]);
        $em->flush();
        $return['code'] = 200;
        $return['data']['countOfUpdated'] = "完成";
        $return['message'] = "完成";
        $response = new JsonResponse();
        $response->setData($return);
        return $response;

        //     try {
        //    transaction start
        //
        //    update user_configuration set flag_data= true where  user_id  = uid and flag_name ='auto_checkin'
        //    transaction commit
        //    } catch() {
        //    transaction roll back
        //    return json:   code: 500, message: 出错了。
        //    }
        //    return json code 200, data: countOfUpdated: 1, message: 完成
    }

    /**
     * @Route("/get")
     * @Template()
     */
    public function getAction() {
        $em = $this->getDoctrine()->getManager();
        $session = $this->get('session');
        $request = $this->get('request');

        //check login
        if (!$session->has('uid')) {
            $return['code'] = 401;
            $return['message'] = "需要登录";

            $response = new JsonResponse();
            $response->setData($return);
            return $response;
        }
        $user_id = $session->get('uid');

        //check mothod
        if ($request->getMethod() != 'GET' || !$request->isXmlHttpRequest()) {
            $return['code'] = 400;
            $return['message'] = "请求方法不对";
            $response = new JsonResponse();
            $response->setData($return);
            return $response;
        }

        //get user config auto_checkin
        $user = $em->getRepository('JiliApiBundle:User')->find($user_id);
        $userConfiguration = $em->getRepository('JiliApiBundle:UserConfigurations')->searchUserConfiguration("auto_checkin", $user);

        $return['code'] = 200;
        $return['data']['flag_data'] = $userConfiguration[0]->getFlagData();
        $response = new JsonResponse();
        $response->setData($return);
        return $response;
    }

}
