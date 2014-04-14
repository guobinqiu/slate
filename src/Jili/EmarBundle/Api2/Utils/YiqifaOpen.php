<?php
namespace Jili\EmarBundle\Api2\Utils;
///////////////////////////////////////////////////////////////////
/*				亿起发开放平台API的入口类						*/
///////////////////////////////////////////////////////////////////

class YiqifaOpen{
    
	/**账号Key处请填写个人应用信息的key**/
    public $consumerKey; 

	/**账号secret处请填写个人应用信息的secret**/
	public $consumerSecret; 

    /**开放平台api的入口**/
	public $gatewayUrl = "http://openapi.yiqifa.com/api2";

	/**输出数据的格式,xml或是json**/
	public $format = "json";
	
	/**API版本号**/
	protected $apiVersion = "2.0";
	
	/**SDK版本号**/
	protected $sdkVersion = "eop-sdk4php";
    
	/**把账户和密码组装在构造方法中**/
	function __construct($key/*=YQF_C_KEY*/, $secret/*=YQF_C_SECRET*/) {        
       $this->consumerKey = $key;
       $this->consumerSecret = $secret;
    }

	/**发送请求**/
	public function curl($url,$consumerKey,$consumerSecret)
	{
		
		$result = YiqifaUtils::sendRequest($url,$consumerKey,$consumerSecret);
        return $result;
	}
	
	public function execute($request)
	{
		
		//组装系统参数
		$sysParams["consumerKey"] = $this->consumerKey;
		$sysParams["v"] = $this->apiVersion;
		$sysParams["format"] = $this->format;
		$sysParams["method"] = $request->getApiMethodName();
		$sysParams["timestamp"] = date("Y-m-d H:i:s");
		$sysParams["partner_id"] = $this->sdkVersion;
	
         //获取业务参数
		
		$apiParams = $request->getApiParams();

		//系统参数放入GET请求串
		$requestUrl = $this->gatewayUrl.'/'.$sysParams["method"].'.'.$sysParams["format"];
		$requestUrl = $requestUrl."?";
	
		foreach ($apiParams as $apiParammKey => $apiParamValue)
		{
			$requestUrl .= "$apiParammKey=" . urlencode($apiParamValue) . "&";
		}
		$requestUrl = substr($requestUrl, 0, -1);
		
		
		//发起HTTP请求
		try
		{	
			$resp = $this->curl($requestUrl, $this->consumerKey,$this->consumerSecret);
			return $resp;
		}
		catch (Exception $e)
		{   
			return $result;
		}
		//解析TOP返回结果
		$respWellFormed = false;
		if ("json" == $this->format)
		{	
			$respObject = json_decode($resp,false);
			var_dump($respObject);
			if (null !== $respObject)
			{
				$respWellFormed = true;
				$result = '';
				foreach ($respObject as $propKey => $propValue)
				{
					$respObject = $propValue;
				}
			}
		}
		else if("xml" == $this->format)
		{
			$respObject = @simplexml_load_string($resp);
			if (false !== $respObject)
			{
				$respWellFormed = true;
			}
		}

		return $respObject;
	}
    
}
?>
