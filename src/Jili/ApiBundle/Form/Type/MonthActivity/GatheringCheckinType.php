<?php
namespace Jili\ApiBundle\Form\Type\MonthActivity;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * 
 **/
class GatheringCheckinType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('token','hidden',array());
    }

    public function getName()
    {
        return 'activityGatheringCheckin';
    }

}

