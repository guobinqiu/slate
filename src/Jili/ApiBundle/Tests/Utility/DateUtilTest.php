<?php

namespace Jili\ApiBundle\Tests\Utility;

use Jili\ApiBundle\Utility\DateUtil;

class DateUtilTest extends\PHPUnit_Framework_TestCase
{
    public function testGetTimeByMonth()
    {
        $year = date('Y');
        $date = DateUtil :: getTimeByMonth(9);
        $this->assertEquals($year.'-09-01 00:00:00', $date['start_time']);
        $this->assertEquals($year.'-09-30 23:59:59', $date['end_time']);

        $date = DateUtil :: getTimeByMonth(8);
        $this->assertEquals($year.'-08-01 00:00:00', $date['start_time']);
        $this->assertEquals($year.'-08-31 23:59:59', $date['end_time']);
    }

    public function testConvertTimeZone()
    {
        $this->assertEquals('2016-01-15 11:00:00', DateUtil::convertTimeZone('2016-01-15 12:00:00', 'Asia/Tokyo', 'Asia/Shanghai'));
        $this->assertEquals('2016-01-15 12:00:00', DateUtil::convertTimeZone('2016-01-15 12:00:00', 'Asia/Tokyo', 'Asia/Tokyo'));
        $this->assertEquals('2016-01-15 13:00:00', DateUtil::convertTimeZone('2016-01-15 12:00:00', 'Asia/Shanghai', 'Asia/Tokyo'));
    }
}
