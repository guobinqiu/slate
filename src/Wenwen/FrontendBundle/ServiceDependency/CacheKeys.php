<?php

namespace Wenwen\FrontendBundle\ServiceDependency;

class CacheKeys {

    public static function getOrderHtmlSurveyListKey($userId) {
        return $userId . '_getOrderHtmlSurveyList';
    }
}