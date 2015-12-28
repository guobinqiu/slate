<?php

namespace Jili\BackendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;

class PanelistEditFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('id', 'hidden', array (
            'required' => false,
            'error_bubbling' => true
        ));

        $builder->add('nick', 'text', array (
            'label' => 'nick',
            'required' => true,
            'constraints' => array (
                new Assert\Length(array (
                    'min' => 2,
                    'minMessage' => '昵称太短了'
                ))
            )
        ));

        $builder->add('birthday', 'text', array (
            'label' => 'birthday',
            'required' => false,
            'read_only' => 'true',
            'constraints' => array (
                new Assert\Date(array (
                    'message' => '请选择正确的生日，包含年月日'
                ))
            )
        ));

        $builder->add('tel', 'text', array (
            'label' => 'telephone',
            'required' => false,
            'constraints' => array (
                new Assert\Length(array (
                    'min' => 11,
                    'max' => 11,
                    'minMessage' => '请输入11位手机号码',
                    'maxMessage' => '请输入11位手机号码'
                )),
                new Assert\Type(array (
                    'type' => 'numeric',
                    'message' => '请输入11位手机号码'
                ))
            )
        ));

        $builder->add('deleteFlag', 'choice', array (
            'required' => true,
            'expanded' => true,
            'multiple' => false,
            'choices' => array (
                '0' => '会员',
                '1' => '注销'
            )
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array (
            //'data_class' => 'Jili\ApiBundle\Entity\User',
            //'data_class' => NULL,
            'csrf_protection' => true,
            'csrf_field_name' => 'token',
            'intention' => 'task_item'
        ));
    }

    public function getName()
    {
        return 'user';
    }
}
