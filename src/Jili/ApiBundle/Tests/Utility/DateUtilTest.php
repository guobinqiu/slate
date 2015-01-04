<?php
namespace Jili\ApiBundle\Tests\Utility;
use Jili\ApiBundle\Utility\DateUtil;

class DateUtilTest extends\PHPUnit_Framework_TestCase {

    /**
    * @group GetTimeByMonth
    */
    public function testGetTimeByMonth() {
        $year = date('Y');
        $date = DateUtil :: getTimeByMonth(9);
        $this->assertEquals($year.'-09-01 00:00:00', $date['start_time']);
        $this->assertEquals($year.'-09-30 23:59:59', $date['end_time']);

        $date = DateUtil :: getTimeByMonth(8);
        $this->assertEquals($year.'-08-01 00:00:00', $date['start_time']);
        $this->assertEquals($year.'-08-31 23:59:59', $date['end_time']);
    }
}
