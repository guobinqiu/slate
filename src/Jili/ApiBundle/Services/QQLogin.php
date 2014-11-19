<?php
namespace Jili\ApiBundle\Services;

use Jili\ApiBundle\OAuth\QQAuth;

class QQLogin 
{

    public function getQQAuth( $appid, $appkey, $token=null) {
        return  new QQAuth($appid, $appkey, $token) ;
    }

}

