<?php
namespace Jili\ApiBundle\Tests\Utility;

use Jili\ApiBundle\Utility\ValidateUtil;

class ValidateUtilTest extends \PHPUnit_Framework_TestCase {

    /**
     * @group issue_682
     */
    public function testValidateMobile() {
        $return = ValidateUtil :: validateMobile('');
        $this->assertFalse($return);
        $return = ValidateUtil :: validateMobile('12');
        $this->assertFalse($return);
        $return = ValidateUtil :: validateMobile('21234567891');
        $this->assertFalse($return);
        $return = ValidateUtil :: validateMobile('13761756201');
        $this->assertTrue($return);
    }
}