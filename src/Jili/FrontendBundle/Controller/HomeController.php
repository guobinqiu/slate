<?php

namespace Jili\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * @Route("/home",requirements={"_scheme"="http"})
 */
class HomeController extends Controller
{
    /**
     * @Route("/index")
     * @Method({ "GET"})
     * @Template
     */
    public function indexAction()
    {
        $request = $this->get('request');
        $logger = $this->get('logger');

        $cookies = $request->cookies;
        $session = $request->getSession();

        if ($cookies->has('jili_rememberme') && !  $session->has('uid')  ) {
            $token = $cookies->get('jili_rememberme');
            $result = $this->get('login.listener')->byToken( $token);
            if( $result !== false && is_object($result) && $result instanceof User ) {
                $session->set('uid', $result->getId() );
                $session->set('nick', $result-> getNick());
            }
        }


        if( $session->has('uid') ) {
            $this->get('session.points')->reset()->getConfirm();
            $this->get('login.listener')->updateSession();
        }

        if(  $cookies->has('jili_nick') &&  !  $session->has('nick') ) {
            $session->set('nick', $cookies->get('jili_nick'));
        }

#        //首页登录
#        $code = '';
#        $email = $request->get('email');
#        $pwd = $request->get('pwd');
#        $arr['email'] = $email;
#        $code = $this->get('login.listener')->login($this->get('request'),$email,$pwd);

#        if($code == "ok"){
#            return $this->redirect($this->generateUrl('_homepage'));
#        }


        //newbie page
        if( $this->get('login.listener')->isNewbie() )  {
            if( $session->get('is_newbie_passed', false) === false ) {
                $arr['is_newbie_passed'] = false;
                $session->set('is_newbie_passed', true) ;
            }
        }

#        $arr['code'] = $code;
        return array();
    }

    /**
     * @Route("/task")
     * @Template
     */
    public function taskAction()
    {
        //任务列表
        $arr = $this->getTaskList();
        return $this->render('JiliFrontendBundle:Home:task.html.twig', $arr);
    }

     private function getTaskList()
    {
        //可以做的任务，签到+游戏+91问问+购物 -cpa
        $taskList = $this->get('session.task_list');
        $taskList->setRequest($this->get('request'));
        $arr = $taskList->compose();

        //advertiserment check
        $arr['adCheck'] = $this->getAdCheckInfo();

        return $arr;
    }

    /**
     * @Route("/checkIn")
     * @Template
     */
    public function checkInAction()
    {
        $taskList = $this->get('session.task_list');
        $arr = array();
        if( $this->container->getParameter('init_one') ===  $taskList->get('checkin_visit') ) {
            //获取签到积分
            $checkInLister = $this->get('check_in.listener');
            $arr['checkinPoint'] = $checkInLister->getCheckinPoint($this->get('request'));

            //获取签到商家
            $arr['arrList'] = $this->checkinList();

            return $this->render('JiliFrontendBundle:Home:checkIn.html.twig', $arr);
        } else {
            return new Response('<!-- already checked in -->');
        }
    }

    private function getUndoTaskList()
    {
        //可以做的任务，签到+游戏+91问问+购物 -cpa
        if( $this->get('session')->has('uid')) {
            $taskList = $this->get('session.task_list');
            $taskList->setRequest($this->get('request'));
            $arr = $taskList->compose();
        }

        //advertiserment check
        $arr['adCheck'] = $this->getAdCheckInfo();

        return $arr;
    }

    /**
     * @Route("/adCheck")
     * @Template
     */
    public function adCheckAction()
    {
        //advertiserment check
        $adCheck = $this->getAdCheckInfo();
        return new Response($adCheck);
    }

    private function getAdCheckInfo()
    {
        //advertiserment check
        $adCheck = "";
        $filename = $this->container->getParameter('file_path_advertiserment_check');
        if (file_exists($filename)) {
            $file_handle = fopen($filename, "r");
            if ($file_handle) {
               if(filesize ($filename)){
                    $adCheck = fread($file_handle, filesize ($filename));
               }
            }
            fclose($file_handle);
        }
        return $adCheck;
    }
}
