<?php

namespace Wenwen\FrontendBundle\Model;

class OwnerType
{
    const DATASPRING = 'dataspring';
    const INTAGE = 'intage';
    const ORGANIC = 'organic';

    public static $allTypes = [ self::DATASPRING, self:: INTAGE, self::ORGANIC ];

    public static function isValid($ownerType) {
        return in_array($ownerType, self::$allTypes);
    }
}
