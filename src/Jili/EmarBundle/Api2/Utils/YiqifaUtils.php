<?php
namespace Jili\EmarBundle\Api2\Utils;
///////////////////////////////////////////////////////////////////
/*			  亿起发开放平台认证的工具类						*/
///////////////////////////////////////////////////////////////////

class YiqifaUtils
{
    const BASE_URL = "http://openapi.yiqifa.com/api2";
    public static $curl_info;
    public static $debug_mode = 0; // 0 : producttion ; 1: debug

    public static function getBaseUrl()
    {
        return YQF_OPEN_URL;
    }

    /**发送请求**/
    public static function sendRequest($url,$key,$secret)
    {
        $au = YiqifaUtils::generateOauth($url,$key,$secret);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: ".$au));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, "http://open.yiqifa.com");
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $result = curl_exec($ch);

        if( self::$debug_mode === 1 ) {
            if(!curl_errno($ch)) {
                self::$curl_info =  curl_getinfo($ch);
            }
        }

        curl_close($ch);

        try {
            @$r = iconv("GBK","UTF-8//IGNORE",$result);
            if(false === $r){
                throw new  \Exception('iconv pasred error');
            }
             //mb_convert_encoding($result, "UTF-8", "GBK");
        } catch(\Exception $e) {
             @$r=mb_convert_encoding($result, "UTF-8", "GBK");
             if( false === $r && null === $r) {
                 throw new \Exception($e->getMessage() .'& mb_convert_encoding parsed error');
             }
        }

        return $r;
    }
   public static function hmacsha1($key,$data)
   {
        $blocksize=64;
        $hashfunc= 'sha1';
        if (strlen($key)>$blocksize)
            $key=pack('H*',$hashfunc($key));
        $key=str_pad($key,$blocksize,chr(0x00));
        $ipad=str_repeat(chr(0x36),$blocksize);
        $opad=str_repeat(chr(0x5c),$blocksize);
        $hmac = pack(
                    'H*',$hashfunc(
                        ($key^$opad).pack(
                            'H*',$hashfunc(
                                ($key^$ipad).$data
                            )
                        )
                    )
                );

        return base64_encode($hmac);
    }

    public static function generateOauth($url,$key,$secret)
    {
        $authparam = self::generateAuthParams($key,$secret);

        $params = array_merge($authparam,self::parseGetParams($url));

        $basestr = self::generateBaseStr($url,$params);

        $tk = $secret."&openyiqifa";

        $sign = self::hmacsha1($tk,$basestr);
        $str = "";
        foreach($authparam as $k=>$v){
            if($str=="") $str .= $k."=\"".urlencode($v)."\"";
            else $str.= (",".$k."=\"".urlencode($v)."\"");

        }

        $str = "OAuth ".$str.",oauth_signature=\"".urlencode($sign)."\"";
        return $str;
    }

    /**auth认证,返回一组数组**/
    public static function generateAuthParams($key,$secret)
    {
        $ts = strtotime("now");
        $nonce = $ts + rand();
        $authparam = array(
            "oauth_consumer_key"=>$key,
            "oauth_signature_method"=>"HMAC-SHA1",
            "oauth_timestamp"=>$ts,
            "oauth_nonce"=>$nonce,
            "oauth_version"=>"1.0",
            "oauth_token"=>"openyiqifa"
        );
        return $authparam;
    }

    public static function generateRequestStr($url)
    {
        $authparam = self::generateAuthParams();

        $params = array_merge($authparam,self::parseGetParams($url));

        $basestr = self::generateBaseStr($url,$params);

        $sign = self::hmacsha1(self::TOKEN_KEYS,$basestr);

        //$params['oauth_signature'] = urlencode($sign);

        return self::constructRequestURL($url).'?oauth_signature='.urlencode($sign)."&".self::normalizeRequestParameters($params);
    }

    public static function generateBaseStr($url,$params)
    {
        $params = self::sortParams($params);

        $basestr = "GET&".urlencode(self::constructRequestURL($url)).'&'.urlencode(self::normalizeRequestParameters($params));
        return $basestr;
    }
    /*********************************/
    /*     对参数值进行url编码       */
    /*********************************/

    public static function normalizeRequestParameters($params)
    {
        $s = "";
        foreach($params as $k=>$v){
            if($s==""){
                $s = $k."=".urlencode($v);
            }else{
                $s = $s."&".$k."=".urlencode($v);
            }
        }
        return $s;
    }
    /*********************************/
    /*       对数组重新排序         */
    /*********************************/
    public static function sortParams($params)
    {
        $keys = array_keys($params);
        sort($keys);
        $newparams = array();
        foreach($keys as $k){
            $newparams[$k] = $params[$k];
        }
        return $newparams;
    }
    /*********************************/
    /*       对url链接进行截取       */
    /*********************************/
    public static function constructRequestURL($url)
    {
        $i = strpos($url,"?");
        if(!$i){
            return $url;
        }else{
            return substr($url,0,$i);
        }

    }
    /*********************************/
    /*解析url链接的参数，返编码后链接*/
    /*********************************/
    public static function parseGetParams($url)
    {
        $params = array();
        $i = strpos($url,"?");

        if(!$i){
            return $params;
        }

        $sp = explode("&",substr($url,$i+1,strlen($url)));

        foreach($sp as $p){
            $spi = explode("=",$p);
            if(count($spi)>1) $params[urldecode($spi[0])] = urldecode($spi[1]);
        }
        return $params;
    }
}
