<?php
namespace Jili\ApiBundle\Utility;
use Jili\ApiBundle\Utility\ClassName;

class ClassNameFactory {

    public static function create($name, $userId) {
        return new ClassName($name, $userId);
    }
}
?>
