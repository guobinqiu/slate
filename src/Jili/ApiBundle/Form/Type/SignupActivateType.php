<?php

namespace Jili\ApiBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Jili\ApiBundle\Validator\Constraints\PasswordRegex;
use Symfony\Component\Validator\Constraints\True;

class SignupActivateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('password', 'repeated',array(
            'type'=>'password',
            'invalid_message' => '2次输入的用户密码不相同',
            'options' => array('attr' => array('class' => 'password')),
            'first_options'=> array(
                'label' =>'密码：',
                'required' => true,
                'error_bubbling'=>false,
                'constraints' => array(
                    new PasswordRegex(),
                )
            ),
            'second_options'=> array(
                'label' =>'确认密码：',
                'required' => true,
                'error_bubbling'=> false
            ),
            'required' => true,
            'error_bubbling'=>false,
        ))
        ->add('agreement', 'checkbox',array(
            'label' =>'我已认真阅读并同意接受',
            'required' => true,
            'value'=> '1',
            'data'=> true,
            'error_bubbling'=> false,
            'invalid_message'=> '请同意接受《积粒网会员协议》',
            'constraints' => array(
                new True()
            )
        ));
    }

    public function getName()
    {
        return '';
    }
}
