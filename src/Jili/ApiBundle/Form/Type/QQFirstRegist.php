<?php

namespace Jili\ApiBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class QQFirstRegist extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('email_id', 'text',array(
                        'required' => true,
                        'error_bubbling'=> false,
                        'constraints' => new Assert\Regex(array('pattern' =>"/^[A-Za-z0-9-_.+%]+$/")),
                        'invalid_message'=> '请正确输入邮箱地址'
                ));
    }
    
    public function getName()
    {
        return 'qqregist';
    }
    
}
