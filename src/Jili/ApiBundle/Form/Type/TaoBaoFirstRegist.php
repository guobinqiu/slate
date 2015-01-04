<?php

namespace Jili\ApiBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class TaoBaoFirstRegist extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('email', 'text',array(
                        'required' => true,
                        'error_bubbling'=> false,
                        'constraints' => new Assert\Regex(array('pattern' =>"/^[0-9a-zA-Z]+@(([0-9a-zA-Z]+)[.])+[a-z]{2,4}$/i")),
                        'invalid_message'=> '请正确输入邮箱地址'
                ));
    }
    
    public function getName()
    {
        return 'taobao_user_regist';
    }
    
}
