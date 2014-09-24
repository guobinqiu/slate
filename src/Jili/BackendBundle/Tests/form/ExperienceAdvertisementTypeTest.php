<?php
namespace Jili\BackendBundle\Tests\Form\Type;

#use Acme\TestBundle\Model\TestObject;
use Jili\BackendBundle\Form\ExperienceAdvertisementType;
use Symfony\Component\Form\Tests\Extension\Core\Type\TypeTestCase;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\Extension\Validator\Type\FormTypeValidatorExtension;

class ExperienceAdvertisementTypeTest extends TypeTestCase
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
     * @group issue430
     **/
    public function testBindValidData()
    {

        $type = new ExperienceAdvertisementType();
        $form = $this->factory->create($type);

        $formData = array(
            'id'=>5,
            'missionTitle'=>'test',
            'missionHall'=>'1',
            'point'=>'4',
            'missionImgUrl'=> "../../web/images/test.jpg",
        );

        $data = array(
            'id'=>5,
            'missionTitle'=>'test',
            'missionHall'=>'1',
            'point'=>'4',
            'missionImgUrl'=> "../../web/images/test.jpg",
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
