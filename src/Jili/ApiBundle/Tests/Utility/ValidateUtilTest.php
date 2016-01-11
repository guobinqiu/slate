<?php

namespace Jili\ApiBundle\Tests\Utility;

use Jili\ApiBundle\Utility\ValidateUtil;

class ValidateUtilTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @group issue_682
     */
    public function testValidateMobile()
    {
        $return = ValidateUtil::validateMobile('');
        $this->assertFalse($return);
        $return = ValidateUtil::validateMobile('12');
        $this->assertFalse($return);
        $return = ValidateUtil::validateMobile('21234567891');
        $this->assertFalse($return);
        $return = ValidateUtil::validateMobile('13761756201');
        $this->assertTrue($return);
    }

    /**
     * @group admin_vote
     */
    public function testValidatePeriod()
    {
        $return = ValidateUtil::validatePeriod(null, null);
        $this->assertTrue($return, 'start time is null, end time is null');
        $return = ValidateUtil::validatePeriod('2015-09-05', '2015-09-04');
        $this->assertFalse($return, 'Start time is later than the end of time');
        $return = ValidateUtil::validatePeriod('2015-09-01', '2015-09-01');
        $this->assertTrue($return, 'Start time is before than the end of time');
    }
}