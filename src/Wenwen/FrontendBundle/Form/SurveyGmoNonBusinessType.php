<?php

namespace Wenwen\FrontendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Wenwen\FrontendBundle\Entity\SurveyGmoNonBusiness;

class SurveyGmoNonBusinessType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('researchId')
            ->add('title')
            ->add('type', 'choice', array(
                'choices' => array(
                    SurveyGmoNonBusiness::TYPE_SS => SurveyGmoNonBusiness::TYPE_SS,
                    SurveyGmoNonBusiness::TYPE_GK => SurveyGmoNonBusiness::TYPE_GK,
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
