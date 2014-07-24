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
    static public function getEmailToken($email)
    {
        $seed = 'ADF93768CF';
        $hash = sha1($email . $seed);
        for ($i = 0; $i < 5; $i++) {
            $hash = sha1($hash);
        }
        return $hash;
    }
    
}
?>
