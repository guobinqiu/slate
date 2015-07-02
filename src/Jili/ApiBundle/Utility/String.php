<?php
namespace Jili\ApiBundle\Utility;

/**
 *
 */
class String
{

    /**
     * @params:  $string  '1232a2342'
     * @usage: list($uid, $adid) = String::explodeUidAdid( $feadback );
     */
    public static function explodeUidAdid($string)
    {
        $r = array('uid'=>0, 'adid'=>0);
        if( strlen($string) > 0)  {
            if(false !== strpos($string,'a')) {
                list( $x, $y) =explode('a',$string);
                $r ['uid'] = (int ) $x;
                $r ['adid'] = (int ) $y;
            };
        }
        return $r;
    }

    public static function buildUidAdid($uid , $adid)
    {
        $uid = intval($uid);
        $adid= intval($adid);
        $r = $uid. 'a'. $adid ;
        return $r;
    }

    //    getRedirectUrlWithUserId
    // 合并后的商家活动， url: e=uid u=uid_adid
    public static function parseChanetCallbackUrl($userinfo, $extinfo)
    {
        $info = explode("_", $userinfo);
        if (is_array($info) && $info[0] == $extinfo) {
            $return = array (
                'user_id' => $extinfo,
                'advertiserment_id' => $info[1]
            );
            return $return;
        }
        return null;
    }
}
