<?php

namespace Jili\ApiBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class CaptchaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
            $builder->add('captcha', 'captcha');
    
//     public function getDefaultOptions(array $options){
//     	return array(
//     			'validation_groups' => array('search'),

//     	);
    }
    public function getName()
    {
        return '';
    }
}
