<?php

namespace Jili\ApiBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AddAdverType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    	$builder->add('large','file',array(
            			'required'=>false, 
            			'error_bubbling'=>true,
            			))
    	        ->add('small','file',array(
    	        		'required'=>false,
    	        		'error_bubbling'=>true,
    	        		));
    }
    public function getName()
    {
        return '';
    }
}
