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
    static  public function explodeUidAdid( $string )
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

    static public function buildUidAdid($uid , $adid) {

        $uid = intval($uid);
        $adid= intval($adid);
        $r = $uid. 'a'. $adid ;
        return $r;
    }

    public static function getEntityName($name, $userid) {
        $suffix = substr($userid,-1,1);
        return "Jili\ApiBundle\Entity\\".sprintf($name.'%02d', $suffix);
    }
}
