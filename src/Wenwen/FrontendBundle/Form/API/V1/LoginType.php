<?php

namespace Wenwen\FrontendBundle\Form\API\V1;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;

class LoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', 'text', array(
                'constraints' => new Assert\NotBlank(array('message' => '请输入您的邮箱地址或手机号')),
                'error_bubbling' => true,
            ))
            ->add('password', 'password', array(
                'constraints' => array(
                    new Assert\NotBlank(array('message' => '请输入您的密码')),
                    new Assert\Length(array('min' => 5, 'max' => 100)),
                    new Assert\Regex(array('pattern' => '/^\w+/')),
                ),
                'error_bubbling' => true,
            ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection' => false,
        ));
    }

    public function getName()
    {
        return 'login';
    }
}
