<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Jili\ApiBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Jili\ApiBundle\OAuth\QQAuth;
use Jili\ApiBundle\Form\Type\QQFirstRegist;

class QQLoginController extends Controller
{
    /**
     * @Route("/qqcallback", name="qq_api_callback")
     */
    public function callBackAction()
    { 
        $request = $this->get('request');
        $code = $request->query->get('code');
        $qq_auth = $this->get('user_qq_login')->getQQAuth($this->container->getParameter('qq_appid'), $this->container->getParameter('qq_appkey'),'');
        if(isset($code) && trim($code)!=''){
            $result=$qq_auth->access_token($this->container->getParameter('callback_url'), $code);
        }
        if(isset($result['access_token']) && $result['access_token']!=''){
            //授权完成，保存登录信息，使用session保存
            $request->getSession()->set('qq_token', $result['access_token']);
            //得到openid
            $qq_auth = $this->get('user_qq_login')->getQQAuth($this->container->getParameter('qq_appid'), $this->container->getParameter('qq_appkey'),$result['access_token']);
            $qq_response = $qq_auth->get_openid();
            $qq_oid = $qq_response['openid'];
            $em = $this->getDoctrine()->getManager();
            $qquser = $em->getRepository('JiliApiBundle:QQUser')->findOneByOpenId($qq_oid);
            //判断是否已经注册过
            if( !empty($qquser)){
                //如果db已有此openid，说明用户已注册过，设成登陆状态，可直接跳转到首页
                $jiliuser = $em->getRepository('JiliApiBundle:User')->find($qquser->getUserId());
                if(empty($jiliuser)){
                    return $this->render('JiliApiBundle::error.html.twig', array('errorMessage'=>'对不起，找不到该用户，请联系客服。'));
                }
                $request->getSession()->set('uid',$jiliuser->getId());
                $request->getSession()->set('nick',$jiliuser->getNick());
                return $this->redirect($this->generateUrl('_homepage'));
            } else {
                //无此用户，说明没有用qq注册过，转去fist_login页面
                $request->getSession()->set('open_id',$qq_oid);
            }
        }else{
            // '授权失败';
            return $this->render('JiliApiBundle::error.html.twig', array('errorMessage'=>'对不起，QQ用户授权失败，请稍后再试。'));
        }
        //跳转到 qqlogin action
        return $this->redirect($this->generateUrl('qq_fist_login'));
    }
    
    /**
     * @Route("/qqlogin", name="qq_api_login")
     */
    public function qqLoginAction()
    {
        $request = $this->get('request');
        $qq_access_token = $request->getSession()->get('qq_token');
        $user_login = $this->get('user_login');
        $user_login->setSession($request->getSession());
        $login_flag = $user_login->checkLoginStatus();
        if($login_flag) {
            return $this->redirect($this->generateUrl('_homepage'));
        } else {
            // 首次qq登陆,到授权页面
            $qq_auth = $this->get('user_qq_login')->getQQAuth($this->container->getParameter('qq_appid'), $this->container->getParameter('qq_appkey'),$qq_access_token);
            $login_url = $qq_auth->login_url($this->container->getParameter('callback_url'), $this->container->getParameter('scope'));
            return  new RedirectResponse($login_url, '301');
        }
    }
    
    /**
     * @Route("/qqRegiste", name="qq_registe")
     */
    public function qqRegisteAction()
    {
        $request = $this->get('request');
        $qqForm = $request->request->get('qqregist');
        $param['email'] = $qqForm['email_id'].'@'.$this->container->getParameter('qq_email_suffix');
        $request->request->set('email',$param['email']);
        $param['pwd'] = $request->request->get('pwd');
        $param['nick'] = 'QQ'.$request->request->get('qqnickname'); 
        $param['open_id'] = $request->getSession()->get('open_id'); // get in session
        $form  = $this->createForm(new QQFirstRegist());
        $form->bind($request );
        if ($form->isValid()) {
            $user_regist = $this->get('user_regist'); 
            $qquser = $user_regist->qq_user_regist($param);
            if(!$qquser){
                //注册失败
                return $this->render('JiliApiBundle::error.html.twig', array('errorMessage'=>'对不起，QQ用户注册失败，请稍后再试。'));
            } 
            //注册成功，登陆并跳转主页
            $code = $this->get('login.listener')->login($request);
            if($code == 'ok') {
                return $this->redirect($this->generateUrl('_homepage'));
            }
        } else {
            //验证不通过
            $code = $form->getErrorsAsString();
        }
        return $this->render('JiliApiBundle:User:qqFirstLogin.html.twig',
                array('email'=>$qqForm['email_id'], 'pwd'=>'','open_id'=>$param['open_id'],'nickname'=>$param['nick'],
                    'sex'=>$request->request->get('sex'),'form' => $form->createView(), 'regcode'=>$code));
    }
    
    /**
     * @Route("/qqbind", name="qq_bind")
     */
    public function qqBindAction()
    {
        $request = $this->get('request');
        $user_bind = $this->get('user_bind');
        $param['nick'] = 'QQ'.$request->request->get('qqnickname'); 
        $param['email'] = $request->request->get('jili_email');
        $param['pwd']= $request->request->get('jili_pwd');
        $param['open_id'] = $request->getSession()->get('open_id'); // get in session
        if(!$param['open_id']){
            return $this->render('JiliApiBundle::error.html.twig', array('errorMessage'=>'对不起，非法操作，请稍后再试。'));
        }
        $request->request->set('pwd', $param['pwd']);
        $request->request->set('email',$param['email']);
        $code = $this->get('login.listener')->login($request);
        if($code == 'ok') {
            $result = $user_bind->qq_user_bind($param);//登陆验证通过，id和pwd没问题，可以直接用来绑定
            return $this->redirect($this->generateUrl('_homepage'));
        }
        $form  = $this->createForm(new QQFirstRegist());
        return $this->render('JiliApiBundle:User:qqFirstLogin.html.twig',
                array('email'=>$request->request->get('email_id'), 'pwd'=>'','open_id'=>$param['open_id'],'nickname'=>$param['nick'],
                    'sex'=>$request->request->get('sex'),'form' => $form->createView(),'bindcode'=>$code));
    }
    
    /**
     * @Route("/qqFistLogin", name="qq_fist_login")
     */
    public function qqFirstLoginAction()
    {
        $request = $this->get('request');
        $qq_token = $request->getSession()->get('qq_token');
        $qq_auth = $this->get('user_qq_login')->getQQAuth($this->container->getParameter('qq_appid'), $this->container->getParameter('qq_appkey'),$qq_token);
        //获取登录用户open id 
        $openid = $request->getSession()->get('open_id');
        if(!$openid){
            $qq_oid = $qq_auth->get_openid();
            $openid = $qq_oid['openid']; 
            $request->getSession()->set('open_id',$openid);
        }
        
        $result = $qq_auth->get_user_info($openid);
        $form  = $this->createForm(new QQFirstRegist());
        return $this->render('JiliApiBundle:User:qqFirstLogin.html.twig',
                array('email'=>'', 'pwd'=>'','open_id'=>$openid,'nickname'=>$result['nickname'],'sex'=>$result['gender'],'form' => $form->createView()));
    }
}
