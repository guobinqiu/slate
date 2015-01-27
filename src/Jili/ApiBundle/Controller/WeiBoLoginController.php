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
use Jili\ApiBundle\OAuth\WeiBoAuth;
use Jili\ApiBundle\Form\Type\WeiBoFirstRegist;

class WeiBoLoginController extends Controller
{
    /**
     * @Route("/weibocallback", name="weibo_api_callback")
     */
    public function callBackAction()
    { 
        $request = $this->get('request');
        //$code = $request->query->get('code');
        //$code = '7fb65a368f31f2d2737bd0346d75591f';
        $weibo_token = $request->getSession()->get('weibo_token');
        $weibo_uid = $request->getSession()->get('weibo_open_id');
        echo $weibo_token."+++".$weibo_uid;
        //没有token，用code取
        if(empty($weibo_token) || empty($weibo_uid)){
            $code = $request->query->get('code');
            //$code = 'b57cd2153ca7e557cdb18ff37ec87291';
            if(isset($code) && trim($code)!=''){
                $weibo_auth = $this->get('user_weibo_login')->getWeiBoAuth($this->container->getParameter('weibo_appid'), $this->container->getParameter('weibo_appkey'),'');
                $result=$weibo_auth->access_token($this->container->getParameter('callback_url'), $code);
                if(isset($result['access_token']) &&  isset($result['uid'])){
                    $weibo_token = $result['access_token'];
                    $weibo_uid = $result['uid'];
                }
            }
        }
        
        //$result['access_token'] = "2.0081rntD0yY7uke2c44d506dsR11ZC";
        //$result['uid'] = '3572980047';
        if(isset($weibo_token) && $weibo_token!='' && $weibo_uid){
            //授权完成，保存token信息，使用session保存
            $request->getSession()->set('weibo_token', $weibo_token);
            $request->getSession()->set('weibo_open_id', $weibo_uid);
            //得到用户基本信息
            $weibo_auth = $this->get('user_weibo_login')->getWeiBoAuth($this->container->getParameter('weibo_appid'), $this->container->getParameter('weibo_appkey'),$weibo_token);
            $weibo_response_user = $weibo_auth->get_user_info($weibo_uid);
            //var_dump($weibo_response_user);
            //$weibo_oid = $weibo_response_user['openid'];
            $em = $this->getDoctrine()->getManager();
            $weibouser = $em->getRepository('JiliApiBundle:WeiBoUser')->findOneByOpenId($weibo_uid);
            //判断是否已经注册过
            if( !empty($weibouser)){
                //如果db已有此openid，说明用户已注册过，设成登陆状态，可直接跳转到首页
                $jiliuser = $em->getRepository('JiliApiBundle:User')->find($weibouser->getUserId());
                $this->get('login.listener')->initSession($jiliuser);
                return $this->redirect($this->generateUrl('_homepage'));
            } else {
                //无此用户，说明没有用weibo注册过，转去first_login页面
                $request->getSession()->set('weibo_name',$weibo_response_user['name']);
                //跳转到 weibologin action
                return $this->redirect($this->generateUrl('weibo_first_login'));
            }
        }else{
            // '授权失败';
            return $this->render('JiliApiBundle::error.html.twig', array('errorMessage'=>'对不起，微博用户授权失败，请稍后再试。'));
        }
    }
    
    /**
     * @Route("/weibologin", name="weibo_api_login")
     */
    public function weiboLoginAction()
    {
        $request = $this->get('request');
        $weibo_access_token = $request->getSession()->get('weibo_token');
        $user_login = $this->get('user_login');
        $user_login->setSession($request->getSession());
        $login_flag = $user_login->checkLoginStatus();
        if($login_flag) {
            return $this->redirect($this->generateUrl('_homepage'));
        } else {
            // 首次weibo登陆,到授权页面
            $weibo_auth = $this->get('user_weibo_login')->getWeiBoAuth($this->container->getParameter('weibo_appid'), $this->container->getParameter('weibo_appkey'),$weibo_access_token);
            $login_url = $weibo_auth->login_url($this->container->getParameter('callback_url'), $this->container->getParameter('scope'));
            return  new RedirectResponse($login_url, '301');
        }
    }
    
    /**
     * @Route("/weiboRegiste", name="weibo_registe")
     */
    public function weiboRegisteAction()
    {
        $em = $this->getDoctrine()->getManager();
        $code = "";
        $request = $this->get('request');
        $weiboForm = $request->request->get('weibo_user_regist');
        //var_dump($weiboForm);
        $param['email'] = $weiboForm['email'];
        $request->request->set('email',$param['email']);
        $param['nick'] = $request->request->get('weibonickname'); 
        $param['pwd'] = $request->request->get('pwd');
        //var_dump($param['pwd']);
        //exit;
        $check_user = $em->getRepository('JiliApiBundle:User')->findOneByEmail($param['email']);
        if($check_user){
            $code = '此账号已存在，请点击下方’已有积粒网账号‘按钮进行绑定!';
        }
        if(empty($param['pwd']) || (strlen($param['pwd'])<6 || strlen($param['pwd'])>20) ){
            $code = '请填写正确的邮箱或密码!';
        }
        $param['open_id'] = $request->getSession()->get('weibo_open_id'); // get in session
        $form  = $this->createForm(new WeiBoFirstRegist());
        $form->bind($request );
        if ($form->isValid() && empty($code)) {
            $check_weibouser = $em->getRepository('JiliApiBundle:WeiBoUser')->findOneByOpenId($param['open_id']);
            if( empty($check_weibouser)){
                $user_regist = $this->get('user_regist'); 
                $weibouser = $user_regist->weibo_user_regist($param);
                if(!$weibouser){
                    //注册失败
                    return $this->render('JiliApiBundle::error.html.twig', array('errorMessage'=>'对不起，微博用户注册失败，请稍后再试。'));
                } 
            }
            //注册成功，登陆并跳转主页
            $code = $this->get('login.listener')->login($request);
            if($code == 'ok') {
                return $this->redirect($this->generateUrl('_homepage'));
            }
        } else {
            //验证不通过
            if(!$check_user){
                $code = '请填写正确的邮箱或密码!';
            }
        }
        return $this->render('JiliApiBundle:User:weiboFirstLogin.html.twig',
                array('email'=>$param['email'], 'pwd'=>'','nickname'=>$param['nick'],'form' => $form->createView(), 'regcode'=>$code));
    }
    
    /**
     * @Route("/weibobind", name="weibo_bind")
     */
    public function weiboBindAction()
    {
        $request = $this->get('request');
        $param['nick'] = $request->request->get('weibonickname'); 
        $param['email'] = $request->request->get('jili_email');
        $param['pwd']= $request->request->get('jili_pwd');
        $param['open_id'] = $request->getSession()->get('weibo_open_id'); // get in session
        if(!$param['open_id']){
            return $this->render('JiliApiBundle::error.html.twig', array('errorMessage'=>'对不起，非法操作，请稍后再试。'));
        }
        $request->request->set('pwd', $param['pwd']);
        $request->request->set('email',$param['email']);
        $code = $this->get('login.listener')->login($request);
        if($code == 'ok') {
            $user_bind = $this->get('user_bind');
            $result = $user_bind->weibo_user_bind($param);//登陆验证通过，id和pwd没问题，可以直接用来绑定
            return $this->redirect($this->generateUrl('_homepage'));
        }
        $form  = $this->createForm(new WeiBoFirstRegist());
        return $this->render('JiliApiBundle:User:weiboFirstLogin.html.twig',
                array('email'=>$param['email'], 'pwd'=>'','nickname'=>$param['nick'],'form' => $form->createView(),'bindcode'=>$code));
    }
    
    /**
     * @Route("/weiboFirstLogin", name="weibo_first_login")
     */
    public function weiboFirstLoginAction()
    {
        $request = $this->get('request');
        $weibo_token = $request->getSession()->get('weibo_token');
        $weibo_openid = $request->getSession()->get('weibo_open_id');
        $weibo_name = $request->getSession()->get('weibo_name');
        if(!$weibo_token || !$weibo_openid || !$weibo_name){
            return $this->render('JiliApiBundle::error.html.twig', array('errorMessage'=>'对不起，非法操作，请在微博完成授权后再试。'));
        }
        $form  = $this->createForm(new WeiBoFirstRegist());
        return $this->render('JiliApiBundle:User:weiboFirstLogin.html.twig',
                array('email'=>'', 'pwd'=>'','open_id'=>$weibo_openid,'nickname'=>$weibo_name,'form' => $form->createView()));
    }
}