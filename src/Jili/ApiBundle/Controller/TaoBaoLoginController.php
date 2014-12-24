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
use Jili\ApiBundle\OAuth\TaoBaoAuth;
use Jili\ApiBundle\Form\Type\TaoBaoFirstRegist;

class TaoBaoLoginController extends Controller
{
    /**
     * @Route("/taobaocallback", name="taobao_login_callback")
     */
    public function callBackAction()
    { 
        $request = $this->get('request');
        $code = $request->query->get('code');
        $code = '3wYSz6OUh60AmCGiW6oiuYTS1477681';
        $taobao_auth = $this->get('user_taobao_login')->getTaoBaoAuth($this->container->getParameter('taobao_appid'), $this->container->getParameter('taobao_appsecret'),'');
        if(isset($code) && trim($code)!=''){
            //$request->getSession()->set('code', $code);
            $result=$taobao_auth->access_token_and_user_info($this->container->getParameter('callback_url'), $code, 'test');//得到token和淘宝用户基本信息
        }
        //var_dump($result);exit;
        if(isset($result['access_token']) && $result['access_token']!=''){
            //授权完成，保存登录信息，使用session保存
            $request->getSession()->set('taobao_token', $result['access_token']);
            //得到openid
            $taobao_openid = $result['taobao_user_id'];
            $em = $this->getDoctrine()->getManager();
            $taobaouser = $em->getRepository('JiliApiBundle:TaoBaoUser')->findOneByOpenId($taobao_openid);
            //判断是否已经注册过
            if( !empty($taobaouser)){
                //如果db已有此openid，说明用户已注册过，设成登陆状态，可直接跳转到首页
                $jiliuser = $em->getRepository('JiliApiBundle:User')->find($taobaouser->getUserId());
                if(empty($jiliuser)){
                    return $this->render('JiliApiBundle::error.html.twig', array('errorMessage'=>'对不起，找不到该用户，请联系客服。'));
                }
                $request->getSession()->set('uid',$jiliuser->getId());
                $request->getSession()->set('nick',$jiliuser->getNick());
                return $this->redirect($this->generateUrl('_homepage'));
            } else {
                //无此用户，说明没有用taobao注册过，转去fist_login页面
                $request->getSession()->set('open_id',$taobao_openid);
                $request->getSession()->set('nickname',$result['taobao_user_nick']);
            }
        }else{
            // '授权失败';
            return $this->render('JiliApiBundle::error.html.twig', array('errorMessage'=>'对不起，淘宝用户授权失败，请稍后再试。'));
        }
        //跳转到 taobaologin action
        return $this->redirect($this->generateUrl('taobao_fist_login'));
    }
    
    /**
     * @Route("/taobaologin", name="taobao_login")
     */
    public function taobaoLoginAction()
    {
        $request = $this->get('request');
        $taobao_access_token = $request->getSession()->get('taobao_token');
        $user_login = $this->get('user_login');
        $user_login->setSession($request->getSession());
        $login_flag = $user_login->checkLoginStatus();
        if($login_flag) {
            return $this->redirect($this->generateUrl('_homepage'));
        } else {
            // 首次taobao登陆,到授权页面
            $taobao_auth = $this->get('user_taobao_login')->getTaoBaoAuth($this->container->getParameter('taobao_appid'), $this->container->getParameter('taobao_appsecret'),$taobao_access_token);
            $login_url = $taobao_auth->login_url($this->container->getParameter('taobao_login_callback_url'),"test" );
            return  new RedirectResponse($login_url, '301');
        }
    }
    
    /**
     * @Route("/taobaoRegiste", name="taobao_registe")
     */
    public function taobaoRegisteAction()
    {
        $request = $this->get('request');
        $taobaoForm = $request->request->get('taobao_user_regist');
        $param['email'] = $taobaoForm['email']; 
        $request->request->set('email',$param['email']);
        $param['nick'] = $request->request->get('taobaonickname'); 
        $param['pwd'] = $request->request->get('pwd');
        $code = true;
        if(empty($param['pwd']) || (strlen($param['pwd'])<6 || strlen($param['pwd'])>20) ){
            $code = false;
        }
        $param['open_id'] = $request->getSession()->get('open_id'); // get in session
        $form  = $this->createForm(new TaoBaoFirstRegist());
        $form->bind($request );
        if ($form->isValid() && $code) {
            $em = $this->getDoctrine()->getManager();
            $check_taobaouser = $em->getRepository('JiliApiBundle:TaoBaoUser')->findOneByOpenId($param['open_id']);
            if( empty($check_taobaouser)){
                $user_regist = $this->get('user_regist'); 
                $taobaouser = $user_regist->taobao_user_regist($param);
                if(!$taobaouser){
                    //注册失败
                    return $this->render('JiliApiBundle::error.html.twig', array('errorMessage'=>'对不起，淘宝用户注册失败，请稍后再试。'));
                } 
            }
            //注册成功，登陆并跳转主页
            $code = $this->get('login.listener')->login($request);
            if($code == 'ok') {
                return $this->redirect($this->generateUrl('_homepage'));
            }
        } else {
            //验证不通过
            $code = '请填写正确的邮箱或密码!';
        }
        return $this->render('JiliApiBundle:User:taobaoFirstLogin.html.twig',
                array('email'=>$param['email'], 'pwd'=>'','open_id'=>$param['open_id'],'nickname'=>$param['nick'],
                   'form' => $form->createView(), 'regcode'=>$code));
    }
    
    /**
     * @Route("/taobaobind", name="taobao_bind")
     */
    public function taobaoBindAction()
    {
        $request = $this->get('request');
        $user_bind = $this->get('user_bind');
        $param['nick'] = 'taobao_'.$request->request->get('taobaonickname'); 
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
            $result = $user_bind->taobao_user_bind($param);//登陆验证通过，id和pwd没问题，可以直接用来绑定
            return $this->redirect($this->generateUrl('_homepage'));
        }
        $form  = $this->createForm(new TaoBaoFirstRegist());
        return $this->render('JiliApiBundle:User:taobaoFirstLogin.html.twig',
                array('email'=>$param['email'], 'pwd'=>'','open_id'=>$param['open_id'],'nickname'=>$param['nick'],
                    'form' => $form->createView(),'bindcode'=>$code));
    }
    
    /**
     * @Route("/taobaoFistLogin", name="taobao_fist_login")
     */
    public function taobaoFirstLoginAction()
    {
        $request = $this->get('request');
        $taobao_token = $request->getSession()->get('taobao_token');
        if(!$taobao_token){
            return $this->render('JiliApiBundle::error.html.twig', array('errorMessage'=>'对不起，非法操作，请在淘宝完成授权后再试。'));
        }
        //$taobao_auth = $this->get('user_taobao_login')->getTaoBaoAuth($this->container->getParameter('taobao_appid'), $this->container->getParameter('taobao_appsecret'),$taobao_token);
        //获取登录用户open id 
        /*$openid = $request->getSession()->get('open_id');
        $openid = "111111";
        if(!$openid){
            $taobao_oid = $taobao_auth->get_openid();
            $openid = $taobao_oid['openid']; 
            $request->getSession()->set('open_id',$openid);
        }*/
        
        //$result = $taobao_auth->get_user_info($openid);
        $result['nickname'] = $request->getSession()->get('nickname');
        $form  = $this->createForm(new TaoBaoFirstRegist());
        return $this->render('JiliApiBundle:User:taobaoFirstLogin.html.twig',
                array('email'=>'', 'pwd'=>'','nickname'=>$result['nickname'],'form' => $form->createView()));
    }
}