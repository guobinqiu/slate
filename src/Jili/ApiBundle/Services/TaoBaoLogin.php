<?php
namespace Jili\ApiBundle\Services;

use Jili\ApiBundle\OAuth\TaoBaoAuth;

class TaoBaoLogin 
{

    public function getTaoBaoAuth( $appid, $appkey, $token=null) {
        return  new TaoBaoAuth($appid, $appkey, $token) ;
    }

}

