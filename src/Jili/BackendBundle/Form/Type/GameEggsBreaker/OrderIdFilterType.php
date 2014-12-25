<?php

namespace Jili\BackendBundle\Form\Type\GameEggsBreaker;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Validator\Constraints\NotBlank;

class OrderIdFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add( 'orderId','text', array(
            'label'=> '订单号',
            'constraints'=> new NotBlank() 
        ));
    }

    public function getName()
    {
        return 'filter_order_id';
    }
}


