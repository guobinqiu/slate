<?php
namespace Jili\ApiBundle\Tests\Utility;
use Jili\ApiBundle\Utility\DateUtil;

class DateUtilTest extends\PHPUnit_Framework_TestCase {

    /**
    * @group GetTimeByMonth
    */
    public function testGetTimeByMonth() {
        $date = DateUtil :: getTimeByMonth(9);
        $this->assertEquals('2014-09-01 00:00:00', $date['start_time']);
        $this->assertEquals('2014-09-30 23:59:59', $date['end_time']);

        $date = DateUtil :: getTimeByMonth(8);
        $this->assertEquals('2014-08-01 00:00:00', $date['start_time']);
        $this->assertEquals('2014-08-31 23:59:59', $date['end_time']);
    }
}
