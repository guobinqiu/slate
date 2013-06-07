<?php

namespace Jili\ApiBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class forgetPassType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('pwd', 'password',array(
            		'label' =>'重置密码:',
        			'required' => false,
                	'error_bubbling'=>true
                ))
                ->add('pwd', 'password',array(
                		'label' =>'确认密码:',
                    	'required' => false,
        	        	'error_bubbling'=>true
                    ));
    }
    

    public function getName()
    {
        return '';
    }
}
