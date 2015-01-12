<?php
namespace Jili\ApiBundle\Form\Type\MonthActivity;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * 
 **/
class GatheringOrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('orderIdentity','text',array(
            'label'=>'订单号',
            'invalid_message' => '订单号不正确',
            'constraints'=> new NotBlank()
        ));
    }

    public function getName()
    {
        return 'activityGatheringOrder';
    }
}
