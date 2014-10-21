<?php
namespace Jili\ApiBundle\Tests\Form\Type;

use Jili\ApiBundle\Form\Type\SignupActivateType;

use Symfony\Component\Form\Tests\Extension\Core\Type\TypeTestCase;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\Extension\Validator\Type\FormTypeValidatorExtension;

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
     * @group issue_381
     **/
    public function testBindValidData()
    {

        $type = new SignupActivateType();
        $form = $this->factory->create($type);

        $formData = array(
            'password'=>array(
                'first'=> '123qwe',
                'second'=> '123qwe',
            ),
            'agreement'=> true,
        );

        $data = array(
            'password' =>'123qwe',
            'agreement'=> true,

        );

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
