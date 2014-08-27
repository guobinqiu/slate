<?php
namespace Jili\FrontendBundle\Tests\Form\Type;

use Jili\FrontendBundle\Form\Type\SignupType;

use Symfony\Component\Form\Tests\Extension\Core\Type\TypeTestCase;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\Extension\Validator\Type\FormTypeValidatorExtension;

use Gregwar\CaptchaBundle\Type\CaptchaType;


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
            ->addTypeExtension(
                new FormTypeValidatorExtension(
                    $this->getMock('Symfony\Component\Validator\ValidatorInterface')
                )
            )
            ->addTypeGuesser(
                $this->getMockBuilder(
                    'Symfony\Component\Form\Extension\Validator\ValidatorTypeGuesser'
                )
                ->disableOriginalConstructor()
                ->getMock()
            )
            ->getFormFactory();

        $this->dispatcher = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $this->builder = new FormBuilder(null, null, $this->dispatcher, $this->factory);
    }

    /**
     * The captcha type not found!!
     * @group issue_448
     * @group debug 
     **/
    public function testBindValidData()
    {

        $type = new SignupType();
        $form = $this->factory->create($type);
        $formData = array (   'signup' =>    array (     'email' => 'alice_nima@gmail.com',     'nickname' => 'alice32',     'captcha' => 'x4x3'/*,     '_token' => 'ce18bf4f139a6821ef48e331579da3284be1cc8e',*/   ),   'login' => 'Sign Up', );

        $data = array (     'email' => 'alice_nima@gmail.com',     'nickname' => 'alice32',     'captcha' => 'x4x3'/*,     '_token' => 'ce18bf4f139a6821ef48e331579da3284be1cc8e',   */);

        $form->bind($formData);
        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($data, $form->getData());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}
