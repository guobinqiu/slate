<?php

namespace Wenwen\FrontendBundle\ServiceDependency;

class CacheKeys {

    const LIFETIME = 60 * 60 * 8;

    public static function getOrderHtmlSurveyListKey($userId) {
        return $userId . '_getOrderHtmlSurveyList';
    }
}