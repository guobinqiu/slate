<?php

namespace Jili\EmarBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EmarWebsitesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // todo:
        $builder
            ->add('webId')
            ->add('webCatid')
            ->add('commission')
            ->add('is_hidden','choice' , array('choices'   => array('0' => '显示', '1' => '隐藏')))
            ->add('is_hot','choice' , array('choices'   => array('1' => '热卖', '0' => '非热卖')))
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
