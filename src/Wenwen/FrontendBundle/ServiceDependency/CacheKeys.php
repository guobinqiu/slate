<?php

namespace Wenwen\FrontendBundle\ServiceDependency;

/**
 * 所有的key放在这里统一管理
 */
class CacheKeys {
    const PROVINCE_LIST = 'province_list';
    const CITY_LIST = 'city_list';
    const LATEST_NEWS_LIST = 'latest_news_list';
    const PRIZE_POINT_BALANCE = 'prize_point_balance';
    const LATEST_PRIZE_NEWS_LIST = 'latest_prize_news_list';
    const IP_LOCATION_PRE = 'ip_location_';
    const IP_LOCATION_TIMEOUT = 1800; // 保存时间秒 半小时
    const REGISTER_FINGER_PRINT_PRE = 'reg_fp_';
    const REGISTER_FINGER_PRINT_TIMEOUT = 120; // One fingerprint only allow one registration in 2 mins

    const ADMIN_RECRUIT_REPORT_MONTHLY = 'admin_recruit_report_monthly';
    const ADMIN_RECRUIT_REPORT_MONTHLY_TIMEOUT = 28800; // 8 hours

    const ADMIN_RECRUIT_REPORT_DAILY = 'admin_recruit_report_daily';
    const ADMIN_RECRUIT_REPORT_DAILY_TIMEOUT = 28800; // 8 hours

    const SURVEY_TOKEN_TTL = 86400;

    private static $panel_sop = 'sop';
    private static $panel_fulcrum = 'fulcrum';
    private static $panel_cint = 'cint';

    public static function getSopTokenKey($surveyId, $userId)
    {
        return self::getTokenKey($surveyId, $userId, self::$panel_sop);
    }

    public static function getFulcrumTokenKey($surveyId, $userId)
    {
        return self::getTokenKey($surveyId, $userId, self::$panel_fulcrum);
    }

    public static function getCintTokenKey($surveyId, $userId)
    {
        return self::getTokenKey($surveyId, $userId, self::$panel_cint);
    }

    private static function getTokenKey($surveyId, $userId, $panel)
    {
        return $panel . '_' . $userId . '_' . $surveyId;
    }
}