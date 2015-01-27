<?php
namespace Jili\ApiBundle\OAuth;
/**
 * WeiBo API client for PHP
 *
 */
class WeiBoAuth
{
	public $api_url='https://api.weibo.com/';

	public function __construct($appid, $appkey, $access_token=NULL){
		$this->appid=$appid;
		$this->appkey=$appkey;
		$this->access_token=$access_token;
	}

	//生成授权网址
	public function login_url($callback_url){
		$params=array(
			'client_id'=>$this->appid,
			'redirect_uri'=>$callback_url,
			'response_type'=>'code'
		);
		return $this->api_url.'oauth2/authorize?'.http_build_query($params);
	}

	//获取access token
	public function access_token($callback_url, $code){
		$params=array(
            'client_id'=>$this->appid,
			'client_secret'=>$this->appkey,
			'grant_type'=>'authorization_code',
			'code'=>$code,
			'redirect_uri'=>$callback_url
		);
		$url=$this->api_url.'oauth2/access_token';
		$result_str=$this->http($url, http_build_query($params), 'POST');
		//$json_r=array();
		//if($result_str!='')parse_str($result_str, $json_r);
		return get_object_vars(json_decode($result_str));
	}

	//根据openid获取用户信息
	public function get_user_info($openid){
		$params=array(
			'uid'=>$openid
		);
		return $this->api('2/users/show.json', $params);
	}

	//发布分享
	public function add_share($openid, $title, $url, $site, $fromurl, $images='', $summary=''){
		$params=array(
			'openid'=>$openid,
			'title'=>$title,
			'url'=>$url,
			'site'=>$site,
			'fromurl'=>$fromurl,
			'images'=>$images,
			'summary'=>$summary
		);
		return $this->api('share/add_share', $params, 'POST');
	}

	//调用接口
	/**
	//示例：根据openid获取用户信息
	**/
	public function api($url, $params=array(), $method='GET'){
		$url=$this->api_url.$url;
		$params['access_token']=$this->access_token;
		//$params['oauth_consumer_key']=$this->appid;
		//$params['format']='json';
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
	private function http($url, $postfields='', $method='GET', $headers=array()){
		$ci=curl_init();
		curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, FALSE); 
		curl_setopt($ci, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($ci, CURLOPT_TIMEOUT, 30);
		if($method=='POST'){
			curl_setopt($ci, CURLOPT_POST, TRUE);
			if($postfields!='')curl_setopt($ci, CURLOPT_POSTFIELDS, $postfields);
		}
		$headers[]='User-Agent: WeiBo.PHP(piscdong.com)';
		curl_setopt($ci, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ci, CURLOPT_URL, $url);
		$response=curl_exec($ci);
		curl_close($ci);
		return $response;
	}
}
