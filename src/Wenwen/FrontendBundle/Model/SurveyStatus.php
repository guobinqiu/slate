<?php

namespace Wenwen\FrontendBundle\Model;

class SurveyStatus
{
    const STATUS_TARGETED = 'targeted';
    const STATUS_INIT = 'init';
    const STATUS_FORWARD = 'forward';
    const STATUS_COMPLETE = 'complete';
    const STATUS_SCREENOUT = 'screenout';
    const STATUS_QUOTAFULL = 'quotafull';
    const STATUS_ERROR = 'error';

    public static $allStatuses = array(
        self::STATUS_TARGETED,
        self::STATUS_INIT,
        self::STATUS_FORWARD,
        self::STATUS_COMPLETE,
        self::STATUS_SCREENOUT,
        self::STATUS_QUOTAFULL,
        self::STATUS_ERROR,
    );

    public static $answerStatuses = array(
        self::STATUS_COMPLETE,
        self::STATUS_SCREENOUT,
        self::STATUS_QUOTAFULL,
        self::STATUS_ERROR,
    );

    public static function isValid($status) {
        return in_array($status, self::$allStatuses);
    }
}
