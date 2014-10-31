<?php

namespace Jili\ApiBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class QQFirstRegist extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('email_id', 'text',array(
                        'required' => true,
                        'error_bubbling'=> false,
                        'invalid_message'=> '请正确输入邮箱地址'
                ));
    }
    
    public function getName()
    {
        return 'qqregist';
    }
}
