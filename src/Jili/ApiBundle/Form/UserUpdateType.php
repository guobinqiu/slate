<?php

namespace Jili\ApiBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class UserUpdateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('nick', 'text',array(
                'required' => false,
                'error_bubbling'=>true
            ))
                ->add('pwd', 'password' ,array(
                        'required' => false,
                        'error_bubbling'=>true
                    ))
                ->add('sex', 'choice', array(
                        'sex' => array('0' => '男', '1' => '女'),
                        'empty_value' => 'Choose an option',
                        'required'    => false,
                        'empty_value' => 'Choose your sex',
                        'empty_data'  => null
                ))
                ->add('birthday', 'text',array(
                        'required' => false,
                        'error_bubbling'=>true
                    ))
                ->add('email', 'text',array(
                        'required' => false,
                        'error_bubbling'=>true
                    ))
                ->add('tel', 'text',array(
                        'required' => false,
                        'error_bubbling'=>true
                    ))
                ->add('city', 'text',array(
                        'required' => false,
                        'error_bubbling'=>true
                    ));
    }

//     public function getDefaultOptions(array $options) {
//     	return array(
//     			'validation_groups' => array('search'),

//     	);
//     }

    public function getName()
    {
        return '';
    }
}
