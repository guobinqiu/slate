<?php

namespace Wenwen\FrontendBundle\Model;

class OwnerType
{
    const DATASPRING = 'dataspring';
    const INTAGE = 'intage';
    const ORGANIC = 'organic';
    static $all = [ self::DATASPRING, self:: INTAGE, self::ORGANIC ];
}
