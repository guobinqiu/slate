<?php

namespace Jili\ApiBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class FirstRegType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
            $builder->add('email', 'text',array(
                'required' => false,
                'error_bubbling'=>true
            ))->add('nick', 'text',array(
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
