<?php

namespace Jili\FrontendBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class SignupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('email', 'email',array(
            'label'=>'邮件地址',
            'invalid_message' => '邮件地址不正确',
            'required' => true,
            'error_bubbling'=>false
        ))->add('nickname', 'text', array(
            'label'=>'昵称',
            'required' => true,
            'error_bubbling'=>false
        ))->add('captcha','captcha', array(
            'label'=>'验证码',
            'invalid_message' => '验证码无效',
            'required' => true,
            'error_bubbling'=>false
        ))
        ;

    }
    public function getName()
    {
        return '';
    }
}
