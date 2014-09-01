<?php
namespace Jili\ApiBundle\Utility;

/**
 *
 **/
class WenwenToken
{
    /**
     *
     */
    public static function getUniqueToken($value)
    {
        $seed = 'ADF93768CF';
        $hash = sha1($value . $seed);
        for ($i = 0; $i < 5; $i++) {
            $hash = sha1($hash);
        }
        return $hash;
    }

}
