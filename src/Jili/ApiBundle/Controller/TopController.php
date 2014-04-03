<?php

namespace Jili\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("/top")
 */
class TopController extends Controller
{

    /**
     * @Route("/index")
     * @Template();
     */
    public function indexAction()
    {
        if ($_SERVER['HTTP_HOST'] == '91jili.com')
            return $this->redirect('https://www.91jili.com');
        $request = $this->get('request');
        $cookies = $request->cookies;
        if ($cookies->has('jili_uid') && $cookies->has('jili_nick')) {
            $this->get('request')->getSession()->set('uid', $cookies->get('jili_uid'));
//            $this->get('request')->getSession()->set('nick', $cookies->get('jili_nick'));
        }

        //首页登录
        $code = '';
        $email = $request->get('email');
        $pwd = $request->get('pwd');
        $arr['email'] = $email;
        $loginLister = $this->get('login.listener');
        $code = $loginLister->login($this->get('request'),$email,$pwd);
        if($code == "ok"){
            return $this->redirect($this->generateUrl('jili_api_top_index'));
        }
        $arr['code'] = $code;

        return $arr;
    }

    /**
     * @Route("/event")
     * @Template();
     */
    public function eventAction()
    {
        //最新动态 :从文件中读取
        $filename = $this->container->getParameter('file_path_recent_point');
        $recentPoint = $this->readFileContent($filename);
        $arr['recentPoint'] = $recentPoint;
        return $this->render('JiliApiBundle:Top:event.html.twig', $arr);
    }

    /**
     * @Route("/ranking")
     * @Template();
     */
    public function rankingAction()
    {
        //排行榜 :从文件中读取
        $filename = $this->container->getParameter('file_path_ranking_month');
        $rankingMonth = $this->readFileContent($filename);
        $filename = $this->container->getParameter('file_path_ranking_year');
        $rankingYear = $this->readFileContent($filename);
        $arr['rankingMonth'] = $rankingMonth;
        $arr['rankingYear'] = $rankingYear;
        return $this->render('JiliApiBundle:Top:ranking.html.twig', $arr);
    }

    /**
     * @Route("/callboard")
     * @Template();
     */
    public function callboardAction()
    {
        //最新公告，取6条
        $em = $this->getDoctrine()->getManager();
        $callboard = $em->getRepository('JiliApiBundle:CallBoard')->getCallboardLimit(6);
        $arr['callboard'] = $callboard;
        return $this->render('JiliApiBundle:Top:callboard.html.twig', $arr);
    }

    /**
     * @Route("/userInfo")
     * @Template();
     */
    public function userInfoAction()
    {
        //个人中心
        $request = $this->get('request');
        $id = $request->getSession()->get('uid');
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('JiliApiBundle:User')->find($id);
        $arr['user'] = $user;

        //确认中的米粒数
        $task =  $em->getRepository('JiliApiBundle:TaskHistory0'. ( $id % 10 ) );
        $arr['confirmPoints'] = $task->getConfirmPoints($id);

        return $this->render('JiliApiBundle:Top:userInfo.html.twig', $arr);
    }

    public function readFileContent($filename) {

        $contents = null;
        if (!file_exists($filename)) {
            //die("指定文件不存在，操作中断!");
            return $contents;
        }

        //读文件内容
        $file_handle = fopen($filename, "r");
        if (!$file_handle) {
            //die("指定文件不能打开，操作中断!");
            return $contents;
        }

        while (!feof($file_handle)) {
            $line = fgets($file_handle);
            if ($line) {
                $item = explode(",", trim($line));
                $contents[] = $item;
            }
        }

        fclose($file_handle);

        return $contents;
    }

}
