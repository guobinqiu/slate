<?php

namespace Jili\BackendBundle\Form\Type\GameEggsBreaker;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Validator\Constraints\NotBlank;

class OrderPostAtFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add( 'beginAt','text', array(
            'label'=> '开始日期',
            'constraints'=> new NotBlank() 
        ))->add( 'finishAt','text', array(
            'label'=> '结止日期',
            'constraints'=> new NotBlank() 
        ));
    }

    public function getName()
    {
        return 'filter_order_post_at';
    }
}



