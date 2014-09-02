<?php

namespace Jili\FrontendBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;

class SignupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('email', 'email',array(
            'label'=>'邮件地址',
            'invalid_message' => '邮件地址不正确',
            'required' => true,
            'error_bubbling'=>false,
            'constraints'=> array(
                new Email(array(
                    'message' => '邮箱"{{ value }}"是无效的.',
                    'checkMX' => true,
                ))
            )
        ))->add('nickname', 'text', array(
            'label'=>'昵称',
            'invalid_message' => '只允许2-20个字符',
            'required' => true,
            'error_bubbling'=>false,
            'constraints'=> array(
                new Length(array('min'=> 2,
                'max'=> 20,
                'minMessage'=> '最少2个字符',
                'maxMessage'=> '最多20个字符') )
            )
        ))->add('captcha','captcha', array(
            'reload'=> true,
            'label'=>'验证码',
            'invalid_message' => '验证码无效',
#            'required' => true,
#            'error_bubbling'=>false
        ));
    }

    /**
     *
     */
    public function getName()
    {
        return 'signup';
    }
}