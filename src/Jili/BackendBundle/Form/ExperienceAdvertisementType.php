<?php

namespace Jili\BackendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ExperienceAdvertisementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('id', 'hidden',array(
                    'required' => false,
                    'error_bubbling'=>true
                    ))
                ->add('missionTitle', 'text',array(
                    'required' => true,
                    'error_bubbling'=>true,
                    'label'=>"任务标题"
                    ))
                 ->add('missionHall', 'choice',array(
                    'required' => true,
                    'error_bubbling'=>true,
                    'label'=>"任务大厅",
                    'choices'=>array(1=>'大厅一',2=>'大厅二'),
                    'expanded'=>true,
                    'multiple'=>false
                    ))
                ->add('point', 'text',array(
                    'required' => true,
                    'error_bubbling'=>true,
                    'label'=>"米粒数"
                    ))
                ->add('missionImgUrl', 'file',array(
                    'required' => false,
                    'error_bubbling'=>true,
                    'label'=>"任务图片"
                    ))
                ->getForm();
    }
    public function getName()
    {
        return '';
    }
}
