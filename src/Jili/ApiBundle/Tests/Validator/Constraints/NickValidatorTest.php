<?php
namespace Jili\ApiBundle\Tests\Validator\Constraints;

use Jili\ApiBundle\Validator\Constraints\NickRegex;
use Jili\ApiBundle\Validator\Constraints\NickRegexValidator;

class NickValidatorTest extends \PHPUnit_Framework_TestCase
{

    public function testValidateFail()
    {
        $hash = '123qweasdzxc';
        $checksum = '';
        $localsum ='72953e9c175e18ac8af3fd675b58b070';

        $constraint = new NickRegex();
        $validator  = new NickRegexValidator();

        $context = $this->getMockBuilder('Symfony\Component\Validator\ExecutionContext')
            ->disableOriginalConstructor()->getMock();

        $context->expects($this->once())
            ->method('addViolation')
            ->with($this->equalTo('昵称不能含特殊符号'));

        $validator->initialize($context);
        $validator->validate('~我的', $constraint);
    }

    public function testValidatePass()
    {
        $constraint = new NickRegex();
        $validator  = new NickRegexValidator();
        $context = $this->getMockBuilder('Symfony\Component\Validator\ExecutionContext')
            ->disableOriginalConstructor()->getMock();

        $context->expects($this->never())
            ->method('addViolation');

        $validator->initialize($context);
        $validator->validate('我', $constraint);
    }
}
