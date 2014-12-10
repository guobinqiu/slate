<?php
namespace Jili\FrontendBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Validator\Constraints\NotBlank;

class GameEggsBreakerTaoBaoOrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('orderId', 'text', array(
            'label'=>'订单号',
            'invalid_message' => '订单号不正确',
            'constraints'=> new NotBlank() 
        ))->add('orderAt', 'date', array(
            'widget'=> 'single_text',
            'label'=> '订单日期',
            'constraints'=> new NotBlank() 
        ));
    }

    public function getName()
    {
        return 'order';
    }
}

