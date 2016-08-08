<?php

namespace Wenwen\FrontendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('nick', 'text', array (
            'label' => '昵称',
            'constraints' => array (
                new Assert\NotBlank(array (
                    'message' => '请输入昵称'
                )),
                new Assert\Length(array (
                    'min' => 1,
                    'max' => 100,
                    'minMessage' => '最少1个字符',
                    'maxMessage' => '最多100个字符'
                ))
            )
        ));

        $builder->add('tel', 'number', array(
            'label' => '手机号码',
            'constraints' => array(
                new Assert\NotBlank(array (
                    'message' => '请输入手机号码'
                )),
            )
        ));

        //上传图片
        //$builder->add('attachment', 'file');

        //对应user对象的userProfile属性
        $builder->add('userProfile', new UserProfileType());
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Jili\ApiBundle\Entity\User'
        ));
    }

    //对应表单属性: $form['user'][...]
    public function getName()
    {
        return 'user';
    }
}
