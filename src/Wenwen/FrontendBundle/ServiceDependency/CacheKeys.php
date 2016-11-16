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
    const IP_LOCATION_TIMEOUT = 86400; // 保存时间秒 24 hours

    const ADMIN_RECRUIT_REPORT_MONTHLY = 'admin_recruit_report_monthly';
    const ADMIN_RECRUIT_REPORT_MONTHLY_TIMEOUT = 86400; // 24 hours

    const ADMIN_RECRUIT_REPORT_DAILY = 'admin_recruit_report_daily';
    const ADMIN_RECRUIT_REPORT_DAILY_TIMEOUT = 86400; // 24 hours

}