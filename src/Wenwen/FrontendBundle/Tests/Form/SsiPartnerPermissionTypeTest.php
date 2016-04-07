<?php
namespace Wenwen\FrontendBundle\Tests\Form;

use Wenwen\FrontendBundle\Form\SsiPartnerPermissionType;
use Symfony\Component\Form\Tests\Extension\Core\Type\TypeTestCase;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\Extension\Validator\Type\FormTypeValidatorExtension;

class SsiPartnerPermissionTypeTest extends TypeTestCase
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
     * @group dev-merge-ui-survey-list-ssi-agreement
     **/
    public function testBindValidData()
    {
        $type = new SsiPartnerPermissionType();

        $form = $this->factory->create($type);

        $formData = array (
            'permission_flag' => '1'
        );

        $form->bind($formData);
        $this->assertTrue($form->isSynchronized());

        $data = $form->getData();
        $this->assertEquals($formData['permission_flag'], $data['permission_flag']);

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}
