<?php

namespace Jili\BackendBundle\Tests\Form;

use Jili\BackendBundle\Form\PanelistSearchType;
use Symfony\Component\Form\Tests\Extension\Core\Type\TypeTestCase;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\Extension\Validator\Type\FormTypeValidatorExtension;

class PanelistSearchTypeTest extends TypeTestCase
{

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        parent::setUp();
        $this->factory = Forms::createFormFactoryBuilder()->addExtensions($this->getExtensions())->addTypeExtension(new FormTypeValidatorExtension($this->getMock('Symfony\Component\Validator\ValidatorInterface')))->addTypeGuesser($this->getMockBuilder('Symfony\Component\Form\Extension\Validator\ValidatorTypeGuesser')->disableOriginalConstructor()->getMock())->getFormFactory();
        $this->dispatcher = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $this->builder = new FormBuilder(null, null, $this->dispatcher, $this->factory);
    }

    /**
     * @group dev-backend_panelist
     **/
    public function testBindValidData()
    {
        $type = new PanelistSearchType();

        $form = $this->factory->create($type);
        $formData = array (
            'user_id' => 1,
            'app_mid' => 1,
            'email' => 'test@test.com',
            'nickname' => 'aaa',
            'mobile_number' => 123,
            'birthday' => '1948-01',
            'registered_from' => '2015-09-02',
            'registered_to' => '2015-09-03',
            'type_registered' => 0,
            'type_withdrawal' => 1
        );

        $form->bind($formData);
        $this->assertTrue($form->isSynchronized());

        $user = $form->getData();

        $this->assertEquals($formData['user_id'], $user['user_id']);
        $this->assertEquals($formData['app_mid'], $user['app_mid']);
        $this->assertEquals($formData['email'], $user['email']);
        $this->assertEquals($formData['nickname'], $user['nickname']);
        $this->assertEquals($formData['mobile_number'], $user['mobile_number']);
        $this->assertEquals($formData['birthday'], $user['birthday']);
        $this->assertEquals($formData['registered_from'], $user['registered_from']);
        $this->assertEquals($formData['registered_to'], $user['registered_to']);
        $this->assertEquals(1, $user['type_registered']);
        $this->assertEquals(1, $user['type_withdrawal']);

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}
