<?php

namespace Jili\ApiBundle\Utility;

/**
 *
 */
class DateUtil
{
    /**
     * @params:  $string  '9'
     * @return: array('start_time'=>'2014-09-01 00:00:00','end_time'=>'2014-09-31 23:59:59'));
     */
    public static function getTimeByMonth($month)
    {
        //取得该月的第一天
        $start_time = date('Y-').sprintf('%02d', intval($month)).'-01';
        $time = strtotime($start_time);
        $days = date('t', $time);

        $date['start_time'] = $start_time.' 00:00:00';
        $date['end_time'] = date('Y-').sprintf('%02d', intval($month)).'-'.sprintf('%02d', intval($days)).' 23:59:59';

        return $date;
    }

    public static function convertTimeZone($dateTime, $from, $to)
    {
        if ($from === $to) {
            return $dateTime;
        }

        $dt = new \DateTime($dateTime, new \DateTimeZone($from));
        $dt->setTimezone(new \DateTimeZone($to));

        return $dt->format('Y-m-d H:i:s');
    }
}
