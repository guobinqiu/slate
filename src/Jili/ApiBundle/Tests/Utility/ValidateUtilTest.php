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

    /**
     * @group dev-merge-ui-set-password
     * @group dev-merge-ui-profile-edit
     */
    public function testValidatePassword()
    {
        $return = ValidateUtil::validatePassword('');
        $this->assertFalse($return, 'password is empty');

        $return = ValidateUtil::validatePassword('aa');
        $this->assertFalse($return, 'password length < 5 ');

        $return = ValidateUtil::validatePassword('aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa');
        $this->assertFalse($return, 'password length > 100 ');

        $return = ValidateUtil::validatePassword('12345');
        $this->assertFalse($return, 'password has no character');

        $return = ValidateUtil::validatePassword('aaaaa');
        $this->assertFalse($return, 'password has no number');

        $return = ValidateUtil::validatePassword('aaaa1');
        $this->assertTrue($return, 'password is ok');
    }
}