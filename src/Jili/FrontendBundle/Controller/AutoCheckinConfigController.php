<?php
namespace Jili\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class AutoCheckinConfigController extends Controller {

    /**
     * @Route("/create")
     * @Template()
     */
    public function createAction() {

        $em = $this->getDoctrine()->getManager();
        $user_id = $request->getSession()->get('uid');

        //check login
        if (!$user_id) {
            $return['code'] = 401;
            $return['message'] = "需要登录";
            return json_encode($return);
        }

        //check mothod
        if ($request->getMethod() != 'PUT' || !$request->isXmlHttpRequest()) {
            $return['code'] = 400;
            $return['message'] = "请求方法不对";
            return json_encode($return);
        }

        $user = $em->getRepository('JiliApiBundle:User')->find($user_id);
        if (!$user) {
            $return['code'] = 402;
            $return['message'] = "该用户不存在";
            return json_encode($return);
        }

        //insert db
        $userConfiguration = new UserConfigurations();
        $userConfiguration->setUser($user);
        $userConfiguration->setFlagName("auto_checkin");
        $userConfiguration->setCreatedAt(date_create(date('Y-m-d H:i:s')));
        $userConfiguration->setFlagData(1);
        $em->persist($userConfiguration);
        $em->flush();

        $return['code'] = 200;
        $return['message'] = "成功" return json_encode($return);

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