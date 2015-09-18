<?php

namespace Jili\BackendBundle\Tests\Form;

use Jili\BackendBundle\Form\VoteType;
use Jili\BackendBundle\Form\VoteChoiceType;
use Symfony\Component\Form\Tests\Extension\Core\Type\TypeTestCase;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\Extension\Validator\Type\FormTypeValidatorExtension;

class VoteTypeTest extends TypeTestCase
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
     * @group mmzhang
     **/
    public function testBindValidData()
    {
        $type = new VoteType();
        $form = $this->factory->create($type);

        //todo:voteChoices
        $formData = array (
            'id' => 1,
            'startTime' => '2015-09-17',
            'endTime' => '2015-09-20',
            'pointValue' => 1,
            'title' => 'test:title',
            'description' => 'test:description',
            'voteChoices' => array (
                0 => array (
                    'answerNumber' => 1,
                    'name' => 'test'
                ),
                1 => array (
                    'answerNumber' => 2,
                    'name' => 'test2'
                )
            ),
            'voteImage' => ''
        );

        $data = array (
            'id' => 1,
            'startTime' => '2015-09-17',
            'endTime' => '2015-09-20',
            'pointValue' => 1,
            'title' => 'test:title',
            'description' => 'test:description',
            'voteChoices' => array (),
            'voteImage' => ''
        );

        $form->bind($formData);
        $this->assertTrue($form->isSynchronized());

        $vote = $form->getData();

        $this->assertEquals($data['startTime'], $vote->getStartTime());
        $this->assertEquals($data['endTime'], $vote->getEndTime());
        $this->assertEquals($data['pointValue'], $vote->getPointValue());
        $this->assertEquals($data['title'], $vote->getTitle());
        $this->assertEquals($data['description'], $vote->getDescription());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}
