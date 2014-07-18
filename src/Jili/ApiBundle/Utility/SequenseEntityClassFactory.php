<?php
namespace Jili\ApiBundle\Utility;

class SequenseEntityClassFactory {

    public static function getClassName($baseClassName, $userId) {
        $suffix = substr($userId, -1, 1);
        $className = 'Jili\ApiBundle\Entity\\' . sprintf($baseClassName . '%02d', $suffix);
        return new $className ();
    }
}
?>
