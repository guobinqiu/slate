<?php

namespace Jili\ApiBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Jili\ApiBundle\Validator\Constraints\PasswordRegex;

class SignupActivateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('password1', 'password',array(
            'label' =>'密码：',
            'required' => true,
            'error_bubbling'=>false,
            'constraints' => array(
                new PasswordRegex(),
            )
        ))
        ->add('password2', 'password',array(
            'type'=>as
            'label' =>'确认密码：',
            'required' => true,
            'error_bubbling'=> false
        ))
        ->add('agreement', 'checkbox',array(
            'label' =>'我已认真阅读并同意接受',
            'required' => true,
            'value'=> '1',
            'data'=> true,
            'error_bubbling'=> false
        ));
    }

    public function getName()
    {
        return '';
    }
}
