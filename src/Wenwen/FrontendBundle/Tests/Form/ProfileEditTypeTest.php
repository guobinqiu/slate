<?php

namespace Wenwen\FrontendBundle\Tests\Form;

use Wenwen\FrontendBundle\Form\ProfileEditType;
use Symfony\Component\Form\Tests\Extension\Core\Type\TypeTestCase;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\Extension\Validator\Type\FormTypeValidatorExtension;

class ProfileEditTypeTest extends TypeTestCase
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
     * @group dev-merge-ui-profile-edit
     **/
    public function testBindValidData()
    {
        $type = new ProfileEditType();

        $form = $this->factory->create($type);

        $formData = array (
            'nick' => 'nick',
            'birthday' => '1990-09-01',
            'tel' => '12345678901',
            'sex' => 1,
            'personalDes' => 'my personalDes',
            'favMusic' => 'my favMusic',
            'monthlyWish' => 'my monthlyWish'
        );

        $form->bind($formData);
        $this->assertTrue($form->isSynchronized());

        $user = $form->getData();

        $this->assertEquals($formData['nick'], $user->getNick());
        $this->assertEquals($formData['birthday'], $user->getBirthday());
        $this->assertEquals($formData['tel'], $user->getTel());
        $this->assertEquals($formData['sex'], $user->getSex());
        $this->assertEquals($formData['personalDes'], $user->getPersonalDes());
        $this->assertEquals($formData['favMusic'], $user->getFavMusic());
        $this->assertEquals($formData['monthlyWish'], $user->getMonthlyWish());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}
