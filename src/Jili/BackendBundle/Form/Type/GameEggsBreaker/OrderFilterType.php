<?php

namespace Jili\BackendBundle\Form\Type\GameEggsBreaker;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Validator\Constraints\NotBlank;

class OrderFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add( 'beginAt','text', array(
            'label'=> '开始日期',
            'constraints'=> new NotBlank() 
        ))->add( 'finishAt','text', array(
            'label'=> '结止日期',
            'constraints'=> new NotBlank() 
        ))->add('auditStatus', 'choice', array(
            'label'=> '审核状态',
            'choices'=> \Jili\FrontendBundle\Entity\GameEggsBreakerTaobaoOrder::getAuditStatusChoices() ,
            'empty_value' => '选一种状态',
            'empty_data'  => \Jili\FrontendBundle\Entity\GameEggsBreakerTaobaoOrder::AUDIT_STATUS_INIT,
            'constraints'=> new NotBlank() 
        ));
    }

    public function getName()
    {
        return 'filter_order';
    }
}



