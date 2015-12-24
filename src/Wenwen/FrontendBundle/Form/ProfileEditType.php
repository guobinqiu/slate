<?php

namespace Wenwen\FrontendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;

class ProfileEditType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('nick', 'text', array (
            'label' => 'nick',
            'required' => true,
            'constraints' => array (
                new Assert\Length(array (
                    'min' => 2,
                    'max' => 20,
                    'minMessage' => '用户昵称为2-20个字符',
                    'maxMessage' => '用户昵称为2-20个字符'
                ))
            ),
            'invalid_message' => '请输入用户昵称'
        ));

        $builder->add('birthday', 'text', array (
            'label' => 'birthday',
            'required' => false,
            'read_only' => 'true',
            'invalid_message' => '请选择正确的生日'
        ));

        $builder->add('tel', 'text', array (
            'label' => 'telephone',
            'required' => false,
            'constraints' => array (
                new Assert\Length(array (
                    'min' => 11,
                    'max' => 11,
                    'minMessage' => '输入的手机格式不正确1',
                    'maxMessage' => '输入的手机格式不正确2'
                )),
                new Assert\Type(array (
                    'type' => 'numeric',
                    'message' => '输入的手机格式不正确3'
                ))
            ),
            'invalid_message' => '输入的手机格式不正确4'
        ));

        $builder->add('sex', 'choice', array (
            'required' => false,
            'expanded' => true,
            'multiple' => false,
            'choices' => array (
                '1' => '男',
                '2' => '女'
            )
        ));

        $builder->add('personalDes', 'textarea', array (
            'label' => 'personalDes',
            'required' => false,
            'attr' => array (
                'rows' => '6',
                'cols' => '50'
            ),
            'constraints' => array (
                new Assert\Length(array (
                    'max' => 512,
                    'maxMessage' => '大于512个文字'
                ))
            ),
            'invalid_message' => 'Invalid personalDes'
        ));

        $builder->add('favMusic', 'text', array (
            'label' => 'favMusic',
            'required' => false,
            'constraints' => array (
                new Assert\Length(array (
                    'max' => 64,
                    'maxMessage' => '大于64个文字'
                ))
            ),
            'invalid_message' => 'Invalid favMusic'
        ));

        $builder->add('monthlyWish', 'text', array (
            'label' => 'monthlyWish',
            'required' => false,
            'constraints' => array (
                new Assert\Length(array (
                    'max' => 64,
                    'maxMessage' => '大于64个文字'
                ))
            ),
            'invalid_message' => 'Invalid monthlyWish'
        ));

        $builder->add('attachment', 'file', array (
            'required' => false,
            'error_bubbling' => true
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array (
            'data_class' => 'Jili\ApiBundle\Entity\User',
            'csrf_protection' => true,
            'csrf_field_name' => 'token',
            'intention' => 'task_item'
        ));
    }

    public function getName()
    {
        return '';
    }
}
