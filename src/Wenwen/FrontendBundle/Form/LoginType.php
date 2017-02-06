<?php

namespace Wenwen\FrontendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;

class LoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', 'email', array(
                'constraints' => new Assert\NotBlank(array('message' => '请输入您的邮箱地址')),
            ))
            ->add('password', 'password', array(
                'constraints' => array(
                    new Assert\NotBlank(array('message' => '请输入您的密码')),
                    new Assert\Length(array('min' => 5, 'max' => 100)),
                    new Assert\Regex(array('pattern' => '/^\w+/')),
                )
            ))
            ->add('fingerprint', 'hidden');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection' => true,
            'intention' => 'login', //显示指明intention的值方便测试登录的testcase生成对应的csrf_token
        ));
    }

    public function getName()
    {
        return 'login';
    }
}
