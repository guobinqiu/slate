<?php
namespace Jili\BackendBundle\Form\Type\ActivityGathering;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * 
 **/
class OrderTotalType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('total','number',array(
            'label'=>'当前订单总数',
        ))->add('total_current','hidden',array());
    }

    public function getName()
    {
        return 'activityGatheringOrder';
    }
}
