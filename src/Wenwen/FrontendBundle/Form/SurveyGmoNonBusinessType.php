<?php

namespace Wenwen\FrontendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SurveyGmoNonBusinessType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('researchId')
            ->add('title')
            ->add('type', 'choice', array(
                'choices' => array(
                    'Self-Study' => 'Self-Study',
                    '概况研究' => '概况研究',
                )
            ))
            ->add('completePoint')
            ->add('screenoutPoint')
            ->add('quotafullPoint')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Wenwen\FrontendBundle\Entity\SurveyGmoNonBusiness'
        ));
    }

    public function getName()
    {
        return 'wenwen_frontendbundle_surveygmononbusinesstype';
    }
}
