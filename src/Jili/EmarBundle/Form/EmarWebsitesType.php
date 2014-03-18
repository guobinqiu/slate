<?php

namespace Jili\EmarBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EmarWebsitesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('webId')
            ->add('commission')
            ->add('position')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Jili\EmarBundle\Entity\EmarWebsites'
        ));
    }

    public function getName()
    {
        return 'jili_emarbundle_emarwebsitestype';
    }
}
