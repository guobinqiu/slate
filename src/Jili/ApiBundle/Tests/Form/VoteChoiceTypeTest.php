<?php

namespace Jili\BackendBundle\Tests\Form;

use Jili\BackendBundle\Form\VoteChoiceType;
use Symfony\Component\Form\Tests\Extension\Core\Type\TypeTestCase;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\Extension\Validator\Type\FormTypeValidatorExtension;

class VoteChoiceTypeTest extends TypeTestCase
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
     * @group admin_vote
     **/
    public function testBindValidData()
    {
        $type = new VoteChoiceType();
        $form = $this->factory->create($type);

        $formData = array (
            'answerNumber' => 1,
            'name' => 'test'
        );

        $data = array (
            'answerNumber' => 1,
            'name' => 'test'
        );

        $form->bind($formData);
        $this->assertTrue($form->isSynchronized());

        $choice = $form->getData();
        $this->assertEquals($data['answerNumber'], $choice->getAnswerNumber());
        $this->assertEquals($data['name'], $choice->getName());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}
