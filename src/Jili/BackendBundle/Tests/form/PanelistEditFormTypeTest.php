<?php

namespace Jili\BackendBundle\Tests\Form;

use Jili\BackendBundle\Form\PanelistEditFormType;
use Symfony\Component\Form\Tests\Extension\Core\Type\TypeTestCase;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\Extension\Validator\Type\FormTypeValidatorExtension;

class PanelistEditFormTypeTest extends TypeTestCase
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
        $type = new PanelistEditFormType();

        $form = $this->factory->create($type);
        $formData = array (
            'id' => 1,
            'nick' => 'aa',
            'birthday' => '2015-09-20',
            'tel' => 12345678901,
            'deleteFlag' => 0
        );

        $form->bind($formData);
        $this->assertTrue($form->isSynchronized());

        $user = $form->getData();

        $this->assertEquals($formData['id'], $user['id']);
        $this->assertEquals($formData['nick'], $user['nick']);
        $this->assertEquals($formData['birthday'], $user['birthday']);
        $this->assertEquals($formData['tel'], $user['tel']);
        $this->assertEquals($formData['deleteFlag'], $user['deleteFlag']);

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}
