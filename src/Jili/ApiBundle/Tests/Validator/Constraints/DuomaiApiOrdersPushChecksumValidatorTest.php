<?php
namespace Jili\ApiBundle\Tests\Validator\Constraints;

use Jili\ApiBundle\Validator\Constraints\DuomaiApiOrdersPushChecksum;
use Jili\ApiBundle\Validator\Constraints\DuomaiApiOrdersPushChecksumValidator;

class DuomaiApiOrdersPushChecksumValidatorTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @group issue_680 
     */
    public function testValidateFail()
    {
        $hash = '123qweasdzxc';
        $checksum = '';
        $localsum ='72953e9c175e18ac8af3fd675b58b070';

        $constraint = new DuomaiApiOrdersPushChecksum();
        $validator  = new DuomaiApiOrdersPushChecksumValidator();

        $context = $this->getMockBuilder('Symfony\Component\Validator\ExecutionContext')
            ->disableOriginalConstructor()->getMock();

        $context->expects($this->once())
            ->method('addViolation')
            ->with($this->equalTo('Checksum  验证失败: 需要%local_checksum%, 得到 %request_checksum%'), $this->equalTo( array( '%request_checksum%'=>$checksum , '%local_checksum%'=>$localsum)));

        $validator->initialize($context);
        $bad_value  = array('hash'=>$hash , 'request'=> array('id'=>'11','field4'=> 'abc', 'field2'=>2.2, 'field3'=> '2015-03-23 16:02:00', 'checksum'=>$checksum));
        $validator->validate($bad_value, $constraint);
    }

    /**
     * @group issue_680 
     */
    public function testValidatePass()
    {
        $hash = '123qweasdzxc';
        $checksum = '72953e9c175e18ac8af3fd675b58b070';
        $constraint = new DuomaiApiOrdersPushChecksum();
        $validator  = new DuomaiApiOrdersPushChecksumValidator();
        $context = $this->getMockBuilder('Symfony\Component\Validator\ExecutionContext')
            ->disableOriginalConstructor()->getMock();

        $context->expects($this->never())
            ->method('addViolation');

        $validator->initialize($context);
        $bad_value  = array('hash'=>$hash , 'request'=> array('id'=>'12','field4'=> 'abc', 'field2'=>2.2, 'field3'=> '2015-03-23 16:02:00', 'checksum'=>$checksum));
        $validator->validate($bad_value, $constraint);
    }
}
