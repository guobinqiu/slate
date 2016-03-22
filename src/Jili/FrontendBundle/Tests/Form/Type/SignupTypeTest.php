<?php
namespace Jili\FrontendBundle\Tests\Form\Type;

use Symfony\Component\Form\Tests\Extension\Core\Type\TypeTestCase;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\Extension\Validator\Type\FormTypeValidatorExtension;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Validator\ConstraintViolationList;

use Gregwar\CaptchaBundle\Type\CaptchaType;
use Jili\FrontendBundle\Form\Type\SignupType;

class SignupActivateTypeTest extends TypeTestCase
{
    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->factory = Forms::createFormFactoryBuilder()
            ->addExtensions($this->getExtensions())
            ->addTypeExtension(new FormTypeValidatorExtension($this->getMock('Symfony\Component\Validator\ValidatorInterface')))
            ->addTypeGuesser($this->getMockBuilder('Symfony\Component\Form\Extension\Validator\ValidatorTypeGuesser')
                    ->disableOriginalConstructor()
                    ->getMock())
            ->getFormFactory();

        $this->dispatcher = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
       $this->builder = new FormBuilder(null, null, $this->dispatcher, $this->factory);
    }

    protected function getExtensions()
    {
        $session = $this->getMockBuilder('Symfony\Component\HttpFoundation\Session\Session')
            ->disableOriginalConstructor()
            ->setMethods(array('get','remove','has','set'))
            ->getMock();

        $session->expects( $this->any())
            ->method('has')
            ->will($this->returnValue(false) );
        $session->expects( $this->any())
            ->method('remove')
            ->will($this->returnValue(null) );
        $session->expects( $this->any())
            ->method('get')
            ->with('gcb_captcha')
            ->will($this->returnValue(array('phrase'=>'x4x3')) );
        $session->expects( $this->any())
            ->method('set')
            ->will($this->returnValue(null) );

        $captchaType = new CaptchaType(
            $session,
            $this->getMockBuilder('Gregwar\CaptchaBundle\Generator\CaptchaGenerator')
                ->disableOriginalConstructor()
                ->getMock(),
            $this->getMock('Symfony\Component\Translation\TranslatorInterface'),
            array(
                'bypass_code'=>null ,
                'width' =>100,
                'height' =>35,
                'length'=> 4,
                'reload'=> false,
                'as_url'=> '',
                'phrase'=>null,
                'distortion'=>null,
                'quality'=>null,
                'background_color'=>null,
                'text_color'=>null,
                'whitelist_key'=> 'whitelist_key_value',
                'bypass_code'=>null,
                'humanity'=>0,
        )
    );


        return array(new PreloadedExtension(array(
            $captchaType->getName() => $captchaType,
        ), array()));

    }

    /**
     * The captcha type not found!!
     * @group issue_448
     **/
    public function testBindValidData()
    {
        $formData = array(   
            'signup' =>    array( 
                'nickname' => 'alice32',
                'email' => 'alice_nima@gmail.com',
                'password'=>array(
                    'first'=> '123qwe',
                    'second'=> '123qwe'
                ),
                'captcha' => 'x4x3', 
                'agreement'=> true,
                'unsubscribe'=> true
            )
        );

        $type = new SignupType();
        $form = $this->factory->create($type, null);
        $form->bind($formData['signup']);
        $this->assertTrue($form->isSynchronized());

        $captcha_config = $form['captcha']->getConfig();
        $this->assertFalse( $captcha_config->getMapped(),'the captcha no in form->getData');

        $this->assertEquals( 'x4x3', $form['captcha']->getData(),'the captcha value should be x4x3');

        $data = array ( 
          'nickname' => 'alice32',
          'email' => 'alice_nima@gmail.com',
          'password'=>'123qwe',
          'unsubscribe'=> true
        );

        // verify the submission and mapping of the form
        $this->assertEquals($data, $form->getData());
        // check the view 
        $view = $form->createView();
        $children = $view->children;
        foreach (array_keys($formData['signup']) as $key) {
            $this->assertArrayHasKey($key, $children);
        }

    }
}
