<?php

namespace Jili\BackendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class GameSeekerPointsPoolType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('points')
            ->add('sendFrequency')
            ->add('isPublished')
            ->add('publishedAt')
            ->add('isValid')
            ->add('updatedAt')
            ->add('createdAt')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Jili\BackendBundle\Entity\GameSeekerPointsPool'
        ));
    }

    public function getName()
    {
        return 'jili_backendbundle_gameseekerpointspooltype';
    }
}
