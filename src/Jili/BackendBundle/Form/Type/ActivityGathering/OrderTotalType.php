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
            'label'=>'总数',
        ));
    }

    public function getName()
    {
        return 'activityGatheringOrder';
    }
}
