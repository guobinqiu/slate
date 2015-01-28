<?php

namespace Jili\BackendBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Validator\Constraints\NotBlank;
use Jili\BackendBundle\Validator\Constraints\GameSeekerRules;

class PointsStrategyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('rules', 'textarea', array(
                'constraints' => array(
                    new NotBlank(),
                    new GameSeekerRules()
                )
            ))
            ->add('whether_clean_current','checkbox', array(
                'label'=> '是否清除当前奖池',
                'required'  => false,
            ));
    }

    public function getName()
    {
        return 'points_strategy';
    }

}
