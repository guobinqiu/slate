<?php

namespace Jili\FrontendBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Jili\ApiBundle\Validator\Constraints\PasswordRegex;
use Symfony\Component\Validator\Constraints\True;


class SignupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('nickname', 'text', array(
            'label'=>'昵称',
            'invalid_message' => '只允许2-20个字符',
            'required' => true,
            'error_bubbling'=>false,
            'constraints'=> array(
                new Length(array(
                    'min'=> 2,
                    'max'=> 20,
                    'minMessage'=> '最少2个字符',
                    'maxMessage'=> '最多20个字符') )
                )
            ))
            ->add('email', 'email',array(
                'label'=>'电子邮件',
                'invalid_message' => '邮件地址不正确',
                'required' => true,
                'error_bubbling'=>false,
                'constraints'=> array(
                    new Email(array('message' => '邮箱"{{ value }}"是无效的.','checkMX' => true,))
                )
            ))
            ->add('password', 'repeated',array(
                'type'=>'password',
                'invalid_message' => '2次输入的用户密码不相同',
                'options' => array('attr' => array('class' => 'password')),
                'first_options'=> array(
                    'label' =>'密码：',
                    'required' => true,
                    'error_bubbling'=>false,
                    'constraints' => array(new PasswordRegex(),)
                ),
                'second_options'=> array(
                    'label' =>'重复密码：',
                    'required' => true,
                    'error_bubbling'=> false
                ),
                'required' => true,
                'error_bubbling'=>false,
            ))
            ->add('captcha','captcha', array(
                'label'=>'验证码',
                'invalid_message' => '验证码无效',
                'required' => true,
                'error_bubbling'=> false,
#                'mapped'=> true
            ))
            ->add('agreement', 'checkbox',array(
                'label' =>'我愿意接收91问问发出的会员邮件',
                'required' => true,
                'value'=> '1',
                'data'=> true,
                'error_bubbling'=> false,
                'invalid_message'=> '请同意接受《积粒网会员协议》',
                'constraints' => array(new True())
            ))
            ->add('unsubscribe', 'checkbox',array(
                'label' =>'我已阅读并接收《会员协议》',
                'required' => true,
                'value'=> '1',
                'data'=> true,
                'error_bubbling'=> false,
                'invalid_message'=> '请同意接受《会员协议》',
                'constraints' => array(new True())
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
