<?php

namespace Wenwen\FrontendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;

class SignupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nick', 'text', array('label' => '昵称'))
            ->add('email', 'email', array(
                'label' => '邮箱',
                'constraints' => new Assert\NotBlank(array('message' => '请输入您的邮箱地址')),
            ))
//            ->add('pwd', 'repeated', array(
//                'type' => 'password',
//                'invalid_message' => '两次输入的密码不一致',
//                'first_options' => array('label' => '密码'),
//                'second_options' => array('label' => '重复密码'),
//                'constraints' => array(
//                    new Assert\NotBlank(array('message' => '请输入您的密码')),
//                    new Assert\Length(array('min' => 5, 'max' => 100)),
//                    new Assert\Regex(array('pattern' => '/^\w+/')),
//                ),
//            ))
            ->add('pwd', 'password', array(
                'constraints' => array(
                    new Assert\NotBlank(array('message' => '请输入您的密码')),
                    new Assert\Length(array('min' => 5, 'max' => 100)),
                    new Assert\Regex(array('pattern' => '/^\w+/')),
                )
            ))

            //对应user对象的userProfile属性
            ->add('userProfile', new UserProfileType())

            ->add('captchaCode', 'captcha', array(
                'captchaConfig' => 'SignupCaptcha'
            ))
            ->add('subscribe', 'checkbox', array(
                'label' =>'我愿意接收91问问发出的会员邮件',
                'data' => true,
                'mapped' => false,
            ))
            ->add('termAccepted', 'checkbox', array(
                'data' => true,
                'constraints' => new Assert\True(array('message' => '只有接受会员协议才能注册')),
                'mapped' => false,
            ))
            ->add('fingerprint', 'hidden', array('mapped' => false));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Wenwen\FrontendBundle\Entity\User',//这里可以不加，但如果是复杂的嵌套类这个地方就要显式指定
            'csrf_protection' => true,
            'intention' => 'register', //名字随便取，即使同一个用户也让这个表单的token和其它表单的token不一致，这样更加安全
            'cascade_validation' => true,//同时验证嵌套的表单
        ));
    }

    public function getName()
    {
        return 'signup';
    }
}
