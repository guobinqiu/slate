<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Jili\ApiBundle\Controller;
use Jili\ApiBundle\OAuth\QQAuth;

class QQLoginController extends Controller{
    
    /**
     * @Route("/qqcallback", name="qq_api_callback")
     */
    public function callBackAction()
    {
        $request = $this->get('request');
        $request->query->get('code');
        $qq_auth = new QQAuth($qq_k, $qq_s, $qq_t);
        if(isset($request->query->get('code')) && trim($request->query->get('code'))!=''){
            $result=$qq_auth->access_token($callback_url, $request->query->get('code'));
        }
        if(isset($result['access_token']) && $result['access_token']!=''){
            // save access_token  todo
            echo '授权完成，请记录<br/>access token：<input size="50" value="',$result['access_token'],'">';
            //保存登录信息，此示例中使用session保存
            $_SESSION['qq_t'] = $result['access_token']; //access token
        }else{
            echo '授权失败';
        }
        echo '<br/><a href="demo.php">返回</a>'; 
        //跳转到 qqlogin action
    }
    
    /**
     * @Route("/qqlogin", name="qq_api_login")
     */
    public function qqLoginAction(){
        // check login status 
        $qq_t=isset($_SESSION['qq_t'])?$_SESSION['qq_t']:'';
        if(logined) {
            if( $qq_oid = getOpenidInDb()){
                // 如果db已有此openid，说明用户已注册过，设成登陆状态，可直接跳转到首页
            } else {
                $qq_auth = new QQAuth($qq_k, $qq_s, $qq_t);
                $qq_oid = $qq->get_openid();
                $openid = $qq_oid['openid']; //获取登录用户open id 
                // db 没有此openid，说明用户第一次用qq登陆，跳转到绑定账号注册页面，在那个页面注册成功后，生成新账号，跳转到首页
            }

            //获取登录用户信息
            $result = $qq->get_user_info($openid);
            var_dump($result); 
        } else {
            // 首次qq登陆,到授权页面
            $qq = new qqPHP($qq_k, $qq_s);
            $login_url = $qq->login_url($callback_url, $scope);
            echo '<a href="',$login_url,'">点击进入授权页面</a>'; 
        }
    }
    
    /**
     * @Route("/qqRegiste", name="qq_api_registe")
     */
    public function qqRegisteAction(){
        
    }
    
    /**
     * @Route("/qqbind", name="qq_api_bind")
     */
    public function qqBindAction(){
        
    }
    /**
     * @Route("/qqFistLoginPageAction", name="qq_api_fist_login_page")
     */
    public function qqFistLoginPageAction(){
        
    }
}