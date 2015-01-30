<?php
namespace Jili\ApiBundle\Services;
use Jili\ApiBundle\OAuth\WeiBoAuth;
class WeiBoLogin
{
    public function getWeiBoAuth( $appid, $appkey, $token=null) {
        return new WeiBoAuth($appid, $appkey, $token) ;
    }
}