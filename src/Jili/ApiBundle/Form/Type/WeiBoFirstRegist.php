<?php

namespace Jili\ApiBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\NotBlank;

class WeiBoFirstRegist extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('email', 'text', array (
            'required' => true,
            'error_bubbling' => false,
            'constraints' => array (
                new NotBlank(),
                new Assert\Email(array (
                    'message' => 'The email "{{ value }}" is not a valid email.'
                ))
            ),
            'invalid_message' => '请正确输入邮箱地址'
        ));
    }

    public function getName()
    {
        return 'weibo_user_regist';
    }
}