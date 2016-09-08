<?php

namespace Wenwen\FrontendBundle\Controller;

use Jili\FrontendBundle\Form\Type\LoginType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Jili\ApiBundle\Entity\IsReadCallboard;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @Route("/user")
 */
class UserController extends Controller
{
    /**
     * @Route("/login", name="_user_login", methods={"GET", "POST"})
     */
    public function loginAction(Request $request)
    {
        $session = $request->getSession();
        if ($session->has('uid')) {
            return $this->redirect($this->generateUrl('_homepage'));
        }

        $form = $this->createForm(new LoginType());

        if ($request->getMethod() == 'POST') {
            $form->bind($request);

            if ($form->isValid()) {
                $formData = $form->getData();

                $em = $this->getDoctrine()->getManager();
                $user = $em->getRepository('JiliApiBundle:User')->findOneBy(array('email' => $formData['email']));

                if ($user == null || !$user->isPwdCorrect($formData['password'])) {
                    $form->addError(new FormError('邮箱或密码错误'));
                    return $this->render('WenwenFrontendBundle:User:login.html.twig', array('form' => $form->createView()));
                }

                if (!$user->emailIsConfirmed()) {
                    $form->addError(new FormError('邮箱尚未激活'));
                    return $this->render('WenwenFrontendBundle:User:login.html.twig', array('form' => $form->createView()));
                }

                $user->setLastLoginIp($request->getClientIp());
                $user->setLastLoginDate(new \DateTime());
                $em->flush();

                $session->set('uid', $user->getId());

                return $this->redirect($this->generateUrl('_homepage'));
            }
        }

        return $this->render('WenwenFrontendBundle:User:login.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/logout", name="_user_logout")
     */
    public function logoutAction(Request $request)
    {
        $request->getSession()->clear();
        return $this->redirect($this->generateUrl('_homepage'));
    }

    /**
     * @Route("/isNewMs/{id}", name="_user_isNewMs", options={"expose"=true}, methods={"GET", "POST"})
     */
    public function isNewMsAction($id)
    {
        $count = $this->notReadMs($id);
        if(  $count>  0){
            return new Response($count);
        }

        $count = $this->notReadCb();
        if( $count > 0) {
            return new Response($count);
        }
        return new Response('');
    }

    /**
	 * @Route("/exchange", name="_user_exchange")
	 */
    public function exchangeAction(Request $request)
    {
        $id = $this->get('request')->getSession()->get('uid');
        if(!$id){
           return $this->redirect($this->generateUrl('_user_login'));
        }

        $type = $request->query->get('type', 0);
        $exchangeType = $request->query->get('exchangeType', 1);
        $page = $request->query->get('p', 1);
        $page_size = $this->container->getParameter('page_num');

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('JiliApiBundle:User')->find($id);
        $arr['user'] = $user;
        if($exchangeType==1){

            // get total count
            $total_count = $em->getRepository('JiliApiBundle:PointsExchange')->getUserExchangeCount($id, $type);
            $page = $page > (int) ceil($total_count / $page_size) ? (int) ceil($total_count / $page_size) : $page;

            //get list
            $exchange = $em->getRepository('JiliApiBundle:PointsExchange')->getUserExchange($id, $type, $page);
            $arr['exchange'] = $exchange;
            $arr['p'] = $page;
            $arr['total'] = $total_count;

        }else{
            return $this->redirect($this->generateUrl('_default_error'));

        }
        $arr['exchangeType'] = $exchangeType;
        $arr['type'] = $type;
        $arr['page_size'] = $page_size;
        return $this->render('WenwenFrontendBundle:Personal:exchangeHistory.html.twig',$arr);
    }

    /**
	 * @Route("/adtaste", name="_user_adtaste")
	 */
    public function adtasteAction(Request $request)
    {
        if(!$this->get('session')->has('uid')){
           return $this->redirect($this->generateUrl('_user_login'));
        }
        $page = $request->query->get('p', 1);
        $type = $request->query->get('type', 0);
        $page_size = $this->container->getParameter('page_num');

        $user_id = $this->get('session')->get('uid');
        $em = $this->getDoctrine()->getManager();
        $total_count = $em->getRepository('JiliApiBundle:TaskHistory0'.($user_id%10))->getTaskHistoryCount($user_id, $type);
        $page = $page > (int) ceil($total_count / $page_size) ? (int) ceil($total_count / $page_size) : $page;

        $this->get('session.my_task_list')->remove(array('alive'));
        $adtaste = $this->get('session.my_task_list')->selTaskHistory($type, $page);

        $arr['p'] = $page;
        $arr['page_size'] = $page_size;
        $arr['total'] = $total_count;
        $arr['type'] = $type;

        $arr['adtaste'] = $adtaste;
        $user = $em->getRepository('JiliApiBundle:User')->find($user_id);
        $arr['user'] = $user;
        return $this->render('WenwenFrontendBundle:Personal:taskHistory.html.twig',$arr);
    }

    /**
	 * @Route("/updateIsRead", name="_user_updateIsRead", options={"expose"=true})
	 */
    public function updateIsReadAction()
    {
        $isRead = '';
        $code = array();
        $request = $this->get('request');
        $id = $request->getSession()->get('uid');
        $sendid = $request->query->get('sendid');
        $em = $this->getDoctrine()->getManager();
        $isreadInfo = $em->getRepository('JiliApiBundle:IsReadCallboard')->isreadInfo($sendid,$id);
        if(empty($isreadInfo)){
            $isRead = new IsReadCallboard();
            $isRead->setSendCbId($sendid);
            $isRead->setUserId($id);
            $em->persist($isRead);
            $em->flush();
            $isRead = $this->container->getParameter('init_one');
        }
        $sendCb = $em->getRepository('JiliApiBundle:SendCallboard')->find($sendid);
        $content = $sendCb->getContent();
        $code[] = array('content'=>$content,'isRead'=>$isRead);
        return new Response(json_encode($code));
    }

    /**
	 * @Route("/updateSendMs", name="_user_updateSendMs", options={"expose"=true})
	 */
    public function updateSendMsAction()
    {
        $request = $this->get('request');
        $id = $request->getSession()->get('uid');
        $sendid = $request->query->get('sendid');
        $showMs = $this->updateSendMs($id,$sendid);
        return new Response(json_encode($showMs));
    }

    /**
	 * @Route("/message/{sid}",requirements={"sid" = "\d+"}, name="_user_message", options={"expose"=true})
	 */
    public function messageAction($sid)
    {
        $id = $this->get('request')->getSession()->get('uid');
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('JiliApiBundle:User')->find($id);
        $arr['user'] = $user;
        if($sid == $this->container->getParameter('init_two')){//公告
            $sendCb = $em->getRepository('JiliApiBundle:SendCallboard')->getSendCb();
            $userCb = $em->getRepository('JiliApiBundle:IsReadCallboard')->getUserIsRead($id);
            $userIsRead = array();
            foreach ($userCb as $keyCb => $valueCb) {
                $userIsRead[$valueCb['sendCbId']] = $valueCb['sendCbId'];
            }
            $reg_date = $user->getRegisterDate()->format('Y-m-d H:i:s');
            foreach ($sendCb as $key => $value) {
                if($value['createtime']->format('Y-m-d H:i:s') > $reg_date){
                    if(array_key_exists($value['id'],$userIsRead))
                        $sendCb[$key]['isRead'] = $this->container->getParameter('init_one');
                    else
                        $sendCb[$key]['isRead'] = '';
                }else{
                    unset($sendCb[$key]);
                }

            }
            $arr['sendCb'] = $sendCb;
            $paginator = $this->get('knp_paginator');
            $arr['pagination'] = $paginator->paginate(
                $sendCb,
                $this->get('request')->query->get('page', 1),
                $this->container->getParameter('page_num')
            );
            $arr['pagination']->setTemplate('WenwenFrontendBundle:Components:_pageNavs2.html.twig');
        }
        if($sid == $this->container->getParameter('init_one')){//消息
            $showMs  = $this->selectSendMs($id);
            $arr['showMs'] = $showMs;
            $paginator = $this->get('knp_paginator');
            $arr['pagination'] = $paginator->paginate(
                $showMs,
                $this->get('request')->query->get('page', 1),
                $this->container->getParameter('page_num')
            );
            $arr['pagination']->setTemplate('WenwenFrontendBundle:Components:_pageNavs2.html.twig');
        }
        $arr['sid'] = $sid;
        return $this->render('WenwenFrontendBundle:Personal:message.html.twig',$arr);
    }

    /**
     * @Route("/countMs", name="_user_countMs", options={"expose"= true}, methods={"POST"})
     */
    public function countMsAction()
    {
        $id = $this->get('request')->getSession()->get('uid');
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('JiliApiBundle:User')->find($id);
        $countCb = $em->getRepository('JiliApiBundle:SendCallboard')->CountAllCallboard($user->getRegisterDate()->format('Y-m-d H:i:s'));
        $countIsCb = $em->getRepository('JiliApiBundle:SendCallboard')->CountIsReadCallboard($id);
        $countMs = $this->countSendMs($id);
        $notRead = intval($countMs[0]['num']) + intval($countCb[0]['num']) - intval($countIsCb[0]['num']);
        return new Response($notRead);
    }

    private function updateSendMs($userid,$sendid)
    {
        $isRead = '';
        $code = array();
        $em = $this->getDoctrine()->getManager();
        $sm = $em->getRepository('JiliApiBundle:SendMessage0'. ($userid % 10) );
        $updateSm = $sm->find($sendid);
        if($updateSm->getReadFlag() == 0){
            $updateSm->setReadFlag($this->container->getParameter('init_one'));
            $em->persist($updateSm);
            $em->flush();
            $isRead = $this->container->getParameter('init_one');
        }
        $code[] = array('content'=>$updateSm->getContent(),'isRead'=>$isRead);
        return $code;
    }

    private function countSendMs($userid)
    {
      $em = $this->getDoctrine()->getManager();
      $sm = $em->getRepository('JiliApiBundle:SendMessage0'. ($userid % 10));
      $countMs = $sm->CountSendMs($userid);
      return $countMs;
    }

    private function selectSendMs($userid)
    {
      return  $this->getDoctrine()->getManager()->getRepository('JiliApiBundle:SendMessage0'. ($userid % 10) )->getSendMsById($userid);
    }

    private function notReadCb()
    {
        $id = $this->get('request')->getSession()->get('uid');
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('JiliApiBundle:User')->find($id);
        $countCb = $em->getRepository('JiliApiBundle:SendCallboard')->CountAllCallboard($user->getRegisterDate()->format('Y-m-d H:i:s'));
        $countIsCb = $em->getRepository('JiliApiBundle:SendCallboard')->CountIsReadCallboard($id);
        $countUserCb = intval($countCb[0]['num']) - intval($countIsCb[0]['num']);
        return $countUserCb;
    }

    private function notReadMs($id)
    {
        $countUserMs = $this->countSendMs($id);
        return $countUserMs[0]['num'];
    }
}