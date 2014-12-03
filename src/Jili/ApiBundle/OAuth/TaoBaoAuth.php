<?php
namespace Jili\ApiBundle\OAuth;
/**
 * QQ API client for PHP
 *
 */
class TaoBaoAuth
{
	public $auth_url='https://oauth.taobao.com/';
    public $auth_sandbox_url='https://oauth.tbsandbox.com/';
    
	public function __construct($appid, $appkey, $access_token=NULL){
		$this->appid=$appid;
		$this->appkey=$appkey;
		$this->access_token=$access_token;
	}

	//生成授权网址
	public function login_url($callback_url, $state){
        $params = array(
            "response_type"	=>	"code",
            "client_id"		=>	$this->appid,
            "redirect_uri"	=>	$callback_url,
            "state"			=>	$state,
            "encode"        =>  'utf-8'
        );
		return $this->auth_url."authorize?".http_build_query($params);
        //https://oauth.taobao.com/authorize?response_type=code&client_id=21234035&redirect_uri=http://www.zocms.com/oauthLogin.php&state=1
	}

	//获取access token
	public function access_token_and_user_info($callback_url, $code, $state){
		$params=array(
			'grant_type'   =>'authorization_code',
			'client_id'    =>$this->appid,
			'client_secret'=>$this->appkey,
			'code'         =>$code,
			'state'        => $state,
			'redirect_uri' =>$callback_url,
            "encode"       =>  'utf-8'
		);
		$url=$this->auth_url.'token?'.http_build_query($params);
		$result_str=$this->http($url);
		$json_r=array();
		if($result_str!='')parse_str($result_str, $json_r);
		return $json_r;
	}

	/**
	//使用refresh token获取新的access token，QQ暂时不支持
	public function access_token_refresh($refresh_token){
	}
	**/

	//获取登录用户的openid
	public function get_openid(){
		$params=array(
			'access_token'=>$this->access_token
		);
		$url='https://graph.qq.com/oauth2.0/me?'.http_build_query($params);
		$result_str=$this->http($url);
		$json_r=array();
		if($result_str!=''){
			preg_match('/callback\(\s+(.*?)\s+\)/i', $result_str, $result_a);
			$json_r=json_decode($result_a[1], true);
		}
		return $json_r;
	}

	//根据openid获取用户信息
	public function get_user_info($openid){
		$params=array(
			'openid'=>$openid
		);
		return $this->api('user/get_user_info', $params);
	}

	//调用接口
	/**
	$result=$qq->api('user/get_user_info', array('openid'=>$openid), 'GET');
	**/
	public function api($url, $params=array(), $method='GET'){
		$url=$this->api_url.$url;
		$params['access_token']=$this->access_token;
		$params['oauth_consumer_key']=$this->appid;
		$params['format']='json';
		if($method=='GET'){
			$result_str=$this->http($url.'?'.http_build_query($params));
		}else{
			$result_str=$this->http($url, http_build_query($params), 'POST');
		}
		$result=array();
		if($result_str!='')$result=json_decode($result_str, true);
		return $result;
	}

	//提交请求
	private function http($url, $postfields='', $method='POST', $headers=array()){
		$ci=curl_init();
		curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, FALSE); 
		curl_setopt($ci, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($ci, CURLOPT_TIMEOUT, 30);
		if($method=='POST'){
			curl_setopt($ci, CURLOPT_POST, TRUE);
			if($postfields!='')curl_setopt($ci, CURLOPT_POSTFIELDS, $postfields);
		}
		$headers[]='User-Agent: TaoBao.PHP(piscdong.com)';
		curl_setopt($ci, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ci, CURLOPT_URL, $url);
		$response=curl_exec($ci);
		curl_close($ci);
		return $response;
	}
}
