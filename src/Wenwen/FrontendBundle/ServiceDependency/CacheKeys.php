<?php

namespace Wenwen\FrontendBundle\ServiceDependency;

/**
 * 所有的key放在这里统一管理
 */
class CacheKeys {
    public static function getOrderHtmlSurveyListKey($userId) {
        return $userId . '_getOrderHtmlSurveyList';
    }
}