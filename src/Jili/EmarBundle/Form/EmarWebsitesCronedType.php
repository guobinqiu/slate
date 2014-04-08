<?php

namespace Jili\EmarBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EmarWebsitesCronedType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('webId')
            ->add('webName')
            ->add('webCatid')
            ->add('logoUrl')
            ->add('webUrl')
            ->add('information')
            ->add('beginDate')
            ->add('endDate')
            ->add('commission')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Jili\EmarBundle\Entity\EmarWebsitesCroned'
        ));
    }

    public function getName()
    {
        return 'jili_emarbundle_emarwebsitescronedtype';
    }
}
