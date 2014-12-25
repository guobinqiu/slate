<?php

namespace Jili\BackendBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Validator\Constraints\NotBlank;

class GameEggsBreakerAuditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add( 'orderId','text', array(
            'read_only'=> true,
            'label'=> '订单号'
        ))->add('orderAt', 'date', array(
            'read_only'=> true,
            'label'=> '订单生成',
            'widget'=>'single_text'
        ))->add('createdAt', 'date', array(
            'read_only'=> true,
            'label'=> '提交订单',
            'widget'=>'single_text'
        ))->add( 'orderPaid', 'money', array(
            'label'=> '订单金额',
            'required'=> true,
            'currency'=> false,
            'constraints'=> array(
                new NotBlank(),
            )
        ))->add('isValid', 'choice', array(
            'choices'=> \Jili\FrontendBundle\Entity\GameEggsBreakerTaobaoOrder::getIsValidChoices() ,
            'required'=> true,
            'empty_value' => '选一种结果',
            'empty_data'  => null
        ))->add('auditBy' ,'choice', array(
            'choices' => array(
                'daisy' => 'Daisy',
                'mandy' => 'Mandy',
                'yuki' => 'Yuki'
            ),
            'required'    => false,
            'empty_value' => 'Choose custom service',
            'empty_data'  => null

        ) );
    }

    public function getName()
    {
        return 'order';
    }
}

