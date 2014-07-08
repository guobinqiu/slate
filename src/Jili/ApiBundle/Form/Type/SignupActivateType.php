<?php

namespace Jili\ApiBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class SignupActivateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('password1', 'password',array(
            'label' =>'密码：',
            'required' => true,
            'error_bubbling'=>true,
            'constraints' => array(
                new NotBlank(),
                new Length(array('min' => 6, 'max'=>20)),
            )
        ))
        ->add('password2', 'password',array(
            'label' =>'确认密码：',
            'required' => true,
            'error_bubbling'=>true,
            'constraints' => array(
                new NotBlank(),
                new Length(array('min' => 6, 'max'=>20)),
            )
        ))
        ->add('agreement', 'checkbox',array(
            'label' =>'我已认真阅读并同意接受',
            'required' => true,
            'value'=> '1',
            'data'=> true,
            'error_bubbling'=>true
        ));
    }

    public function getName()
    {
        return '';
    }
}
