<?php

namespace Jili\BackendBundle\Tests\Form;

use Jili\BackendBundle\Form\VoteType;
use Jili\BackendBundle\Form\VoteChoiceType;
use Symfony\Component\Form\Tests\Extension\Core\Type\TypeTestCase;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\Extension\Validator\Type\FormTypeValidatorExtension;
use Jili\ApiBundle\Entity\Vote;
use Jili\ApiBundle\Entity\VoteChoice;

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
     **/
    public function testBindValidData()
    {
        $type = new VoteType();
        $vote = new Vote();

        $form = $this->factory->create($type, $vote);

        $formData = array (
            'id' => 1,
            'startTime' => '2015-09-17',
            'endTime' => '2015-09-20',
            'pointValue' => 1,
            'title' => 'test:title',
            'description' => 'test:description',
            'stashData' => 'test:test',
            'voteImage' => ''
        );

        $form->bind($formData);
        $this->assertTrue($form->isSynchronized());

        $vote = $form->getData();
        $this->assertEquals($formData['startTime'], $vote->getStartTime());
        $this->assertEquals($formData['endTime'], $vote->getEndTime());
        $this->assertEquals($formData['pointValue'], $vote->getPointValue());
        $this->assertEquals($formData['title'], $vote->getTitle());
        $this->assertEquals($formData['description'], $vote->getDescription());
        $this->assertEquals($formData['stashData'], $vote->getStashData());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}
