<?php

namespace Jili\ApiBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AdActivityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id','hidden'  )
            ->add('title','hidden' )
            ->add('description' ,'hidden' )
            ->add('startedAt','date',array(
                'input'=>'datetime',
                'widget'=> 'single_text',
                'format' => 'yyyy-MM-dd',
                'read_only'=>true,
                'label'=>'Started At',
            ) )
            ->add('finishedAt','date' , array(
                'input'=>'datetime',
                'read_only'=>true,
                'widget'=> 'single_text',
                'label'=>'Finished At'
            ) )
            ->add('percentage','percent', array(
                'label'=>'Percentage',
                ) )
                ->add('isDeleted', 'choice' , array('choices'=> array( 0=>'有效' , 1=>'无效'),
                'label'=>'Is Deleted'
                ),array('preferred_choices' => 0  ) )
                ->add('isHidden',  'choice', array('choices'=> array( 0=> '显示', 1=> '隐藏'),
                'label'=>'Is Hidden'
            ) ,array('preferred_choices' => 0) )
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Jili\ApiBundle\Entity\AdActivity'
        ));
    }

    public function getName()
    {
        return 'activity';
    }
}
