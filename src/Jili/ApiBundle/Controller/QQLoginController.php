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
        $code = "57226E1F71B04CAA6E139F23CAC64166";
        $qq_access_token = $request->getSession()->get('qq_access_token');
        $qq_auth = new QQAuth($this->container->getParameter('qq_appid'), $this->container->getParameter('qq_appkey'),$qq_access_token);
        if(isset($code) && trim($code)!=''){
            $result=$qq_auth->access_token($this->container->getParameter('callback_url'), $code);
        }
        if(isset($result['access_token']) && $result['access_token']!=''){
            // 授权完成，保存登录信息，此示例中使用session保存
            $request->getSession()->set('qq_token', $result['access_token']);
            //$_SESSION['qq_t'] = $result['access_token']; //access token
        }else{
            //echo '授权失败';
        }
        //echo '<br/><a href="demo.php">返回</a>'; 
        //跳转到 qqlogin action
        return $this->redirect($this->generateUrl('qq_fist_login'));
    }
    
    /**
     * @Route("/qqlogin", name="qq_api_login")
     */
    public function qqLoginAction(){
        // check login status 
        $request = $this->get('request');
        $qq_access_token = $request->getSession()->get('qq_access_token');
        //$qq_t=isset($_SESSION['qq_t'])?$_SESSION['qq_t']:'';
        $user_login = $this->get('user_login');
        $login_flag = $user_login->checkLoginStatus();
        if($login_flag) {
            echo "has logined";
            exit;
            //if( $qq_oid = getOpenidInDb()){
                // 如果db已有此openid，说明用户已注册过，设成登陆状态，可直接跳转到首页
            //} else {
            //    $qq_auth = new QQAuth($qq_k, $qq_s, $qq_t);
            //    $qq_oid = $qq->get_openid();
            //    $openid = $qq_oid['openid']; //获取登 录用户open id 
                // db 没有此openid，说明用户第一次用qq登陆，跳转到绑定账号注册页面，在那个页面注册成功后，生成新账号，跳转到首页
                // 通过api 得到 qq 昵称
                // qqFirstLoginAction
            //}

            //获取登录用户信息
            //$result = $qq->get_user_info($openid);
            //var_dump($result); 
        } else {
            // 首次qq登陆,到授权页面
            $qq = new QQAuth($qq_k, $qq_s);
            $callback_url = "http://91jili.com/";
            $login_url = $qq->login_url($callback_url, $this->container->getParameter('scope'));
            var_dump($login_url);exit;
            $response = new RedirectResponse($login_url, '301');
            //echo '<a href="',$login_url,'">点击进入授权页面</a>'; 
        }
    }
    
    /**
     * @Route("/qqRegiste", name="qq_registe")
     */
    public function qqRegisteAction()
    {
        echo "regist";
        $request = $this->get('request');
        $qqForm = $request->request->get('qqregist');
        $param['email'] = $qqForm['email_id'].'@'.$this->container->getParameter('qq_email_suffix');
        $request->request->set('email',$param['email']);
        $param['pwd'] = $request->request->get('pwd');
        $param['nick'] = 'QQ'.$request->request->get('qqnickname'); 
        $param['open_id'] = $request->getSession()->get('open_id'); // get in session
        $request->request->set('pwd', $param['pwd']);
        $form  = $this->createForm(new QQFirstRegist());
        $form->bind($request);
        echo 222;
        if ($form->isValid()) {
            $user_regist = $this->get('user_regist'); 
            $qquser = $user_regist->qq_user_regist($param);
            $inst_id = $qquser->getid();
            if(!isset($inst_id)){
                $code = "registe fail, please try again later";
            } else {
                $code = "ok";
            }
            echo $code;exit;
            if ($code = "ok"){
                $code = $this->get('login.listener')->login($request);
                if($code == 'ok') {
                    $code_redirect = '301';
                    $current_url = '/';
                    $response = new RedirectResponse($current_url, $code_redirect);
                    return $response;
                }
            }
        }
        echo  $form->getErrorsAsString();
        return $this->render('JiliApiBundle:User:qqFirstLogin.html.twig',
                array('email'=>$qqForm['email_id'], 'pwd'=>'','open_id'=>$param['open_id'],'nickname'=>$param['nick'],'sex'=>$request->request->get('sex'),'form' => $form->createView()));
        //return $this->redirect($this->generateUrl('qq_fist_login'));
    }
    
    /**
     * @Route("/qqbind", name="qq_bind")
     */
    public function qqBindAction()
    {
        echo "bind";
        $request = $this->get('request');
        $user_bind = $this->get('user_bind');
        $param['nick'] = 'QQ'.$request->request->get('qqnickname'); 
        $param['email'] = $request->request->get('jili_email');
        $param['pwd']= $request->request->get('jili_pwd');
        $param['open_id'] = $request->getSession()->get('open_id'); // todo get in session
        $request->request->set('pwd', $param['pwd']);
        $request->request->set('email',$param['email']);
        $code = $this->get('login.listener')->login($request);
        // Apibundle/Ses/UserBind.php(call UserBindtervices/UserBind.php(call UserBindto bind jili's id)
        // Apibundle/Services/UserLogin.php(call UserLogin to login) or call UserController _user_login
        echo $code;
        if($code == 'ok') {
            $result = $user_bind->qq_user_bind($param);//登陆验证通过，id和pwd没问题，可以直接用来绑定
            $code_redirect = '301';
            $current_url = '/'; 
            $response = new RedirectResponse($current_url, $code_redirect);
            echo $code;
            exit;
            return $response;
        }
        $form  = $this->createForm(new QQFirstRegist());
        return $this->render('JiliApiBundle:User:qqFirstLogin.html.twig',
                array('email'=>$request->request->get('email_id'), 'pwd'=>'','open_id'=>$param['open_id'],'nickname'=>$param['nick'],
                    'sex'=>$request->request->get('sex'),'form' => $form->createView(),'bindcode'=>$code));
        //return $this->redirect($this->generateUrl('qq_fist_login'));
    }
    
    /**
     * @Route("/qqFistLogin", name="qq_fist_login")
     */
    public function qqFirstLoginAction()
    {
        $request = $this->get('request');
        $qq_token = $request->getSession()->get('qq_token');
        var_dump($qq_token);
        if(!isset($qq_token)){
            $qq_token = $request->getSession()->set('qq_token','test token');
        }
        $qq_auth = new QQAuth($this->container->getParameter('qq_appid'), $this->container->getParameter('qq_appkey'),$qq_token);
        $qq_oid = $qq_auth->get_openid();
        $openid = $qq_oid['openid']; //获取登录用户open id 
        $request->getSession()->set('open_id',$openid);
        $result = $qq_auth->get_user_info($openid);
        var_dump($result); 
        $form  = $this->createForm(new QQFirstRegist());
        //$this->get('login.listener')->initSession( $user );
        return $this->render('JiliApiBundle:User:qqFirstLogin.html.twig',
                array('email'=>'', 'pwd'=>'','open_id'=>$openid,'nickname'=>$result['nickname'],'sex'=>$result['gender'],'form' => $form->createView()));
    }
}