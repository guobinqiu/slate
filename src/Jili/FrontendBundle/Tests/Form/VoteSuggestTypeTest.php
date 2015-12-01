<?php

namespace Jili\FrontendBundle\Tests\Form;

use Jili\FrontendBundle\Form\VoteSuggestType;
use Symfony\Component\Form\Tests\Extension\Core\Type\TypeTestCase;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\Extension\Validator\Type\FormTypeValidatorExtension;

class VoteSuggestTypeTest extends TypeTestCase
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

    /**
     * @group user_vote
     **/
    public function testBindValidData()
    {
        $type = new VoteSuggestType();

        $form = $this->factory->create($type);

        $formData = array (
            'title' => '【生活】你认为红楼梦中最吸引人的角色是？',
            'description' => '每个人读红楼都会有不同的感受，请选出最吸引你的那个人物。',
            'option1' => '晴雯',
            'option2' => '王熙凤',
            'option3' => '贾宝玉',
            'option4' => '林黛玉',
            'option5' => '薛宝钗',
            'option6' => '史湘云',
            'option7' => '王熙凤',
            'option8' => '贾探春',
            'option9' => '秦可卿',
            'option10' => '其他'
        );

        $form->bind($formData);
        $this->assertTrue($form->isSynchronized());

        $vote = $form->getData();

        $this->assertEquals($formData['title'], $vote['title']);
        $this->assertEquals($formData['description'], $vote['description']);
        $this->assertEquals($formData['option1'], $vote['option1']);
        $this->assertEquals($formData['option2'], $vote['option2']);
        $this->assertEquals($formData['option3'], $vote['option3']);
        $this->assertEquals($formData['option4'], $vote['option4']);
        $this->assertEquals($formData['option5'], $vote['option5']);
        $this->assertEquals($formData['option6'], $vote['option6']);
        $this->assertEquals($formData['option7'], $vote['option7']);
        $this->assertEquals($formData['option8'], $vote['option8']);
        $this->assertEquals($formData['option9'], $vote['option9']);
        $this->assertEquals($formData['option10'], $vote['option10']);

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}
