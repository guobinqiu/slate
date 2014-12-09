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
        ))->add('orderPaid', 'money', array(
            'currency'=> false,
            'divisor' => 100,
            'label'=> '支付金额',
            'constraints'=> new NotBlank() 
        ));
    }

    public function getName()
    {
        return 'order';
    }
}

