<?php
namespace Jili\ApiBundle\Tests\Validator\Constraints;

use Jili\ApiBundle\Validator\Constraints\PasswordRegex;
use Jili\ApiBundle\Validator\Constraints\PasswordRegexValidator;

class PasswordRegexValidatorTest extends \PHPUnit_Framework_TestCase
{
    public function testValidatePass()
    {
        $constraint = new PasswordRegex();
        $validator  = new PasswordRegexValidator();
        $context = $this->getMockBuilder('Symfony\Component\Validator\ExecutionContext')
            ->disableOriginalConstructor()->getMock();

        $context->expects($this->never())
            ->method('addViolation');
        $validator->initialize($context);
        $value='123asd';
        $validator->validate($value, $constraint);
    }

    public function testValidatePasswordWithSpecialChars()
    {
        $constraint = new PasswordRegex();
        $validator  = new PasswordRegexValidator();

        $context = $this->getMockBuilder('Symfony\Component\Validator\ExecutionContext')
            ->disableOriginalConstructor()->getMock();

        $context->expects($this->never())
            ->method('addViolation');

        $validator->initialize($context);
        $value='1111aaa#$%';
        $validator->validate($value, $constraint);
    }
    public function testPasswordEmpty()
    {
        $constraint = new PasswordRegex();
        $validator  = new PasswordRegexValidator();

        $context = $this->getMockBuilder('Symfony\Component\Validator\ExecutionContext')
            ->disableOriginalConstructor()->getMock();

        $context->expects($this->once())
            ->method('addViolation')
            ->with($this->equalTo('password_is_required') );

        $validator->initialize($context);
        $value='';
        $validator->validate($value, $constraint);
    }

    public function testPasswordShort()
    {
        $constraint = new PasswordRegex();
        $validator  = new PasswordRegexValidator();

        $context = $this->getMockBuilder('Symfony\Component\Validator\ExecutionContext')
            ->disableOriginalConstructor()->getMock();

        $context->expects($this->once())
            ->method('addViolation')
            ->with($this->equalTo('password_is_too_short') );

        $validator->initialize($context);
        $value='3';
        $validator->validate($value, $constraint);
    }
    public function testPasswordLong()
    {
        $constraint = new PasswordRegex();
        $validator  = new PasswordRegexValidator();

        $context = $this->getMockBuilder('Symfony\Component\Validator\ExecutionContext')
            ->disableOriginalConstructor()->getMock();

        $context->expects($this->once())
            ->method('addViolation')
            ->with($this->equalTo('password_is_too_long') );

        $validator->initialize($context);
        $value=str_repeat('3', 201);
        $validator->validate($value, $constraint);
    }
    public function testPasswordNoNumber()
    {
        $constraint = new PasswordRegex();
        $validator  = new PasswordRegexValidator();

        $context = $this->getMockBuilder('Symfony\Component\Validator\ExecutionContext')
            ->disableOriginalConstructor()->getMock();

        $context->expects($this->once())
            ->method('addViolation')
            ->with($this->equalTo('password_is_invalid') );

        $validator->initialize($context);
        $value='aaaaaa';
        $validator->validate($value, $constraint);
    }
    public function testPasswordNoAlpha()
    {
        $constraint = new PasswordRegex();
        $validator  = new PasswordRegexValidator();

        $context = $this->getMockBuilder('Symfony\Component\Validator\ExecutionContext')
            ->disableOriginalConstructor()->getMock();

        $context->expects($this->once())
            ->method('addViolation')
            ->with($this->equalTo('password_is_invalid') );

        $validator->initialize($context);
        $value='1111111';
        $validator->validate($value, $constraint);
    }

}
