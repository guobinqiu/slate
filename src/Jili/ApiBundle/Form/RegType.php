<?php

namespace Jili\ApiBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class RegType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('attachment','file',array('required'=>false, 'error_bubbling'=>true))
                ->add('nick', 'text',array(
//             		'label' =>'',
        			'required' => false,
                	'error_bubbling'=>true
                ))
                ->add('sex', 'choice', array(
                        'choices' => array('0' => '男', '1' => '女'),
//                 		'label' =>'性别:',
                		'empty_value' => 'Choose an option',
                		'required'    => false,
                		'empty_value' => 'Choose your sex',
                		'empty_data'  => null,
                		'multiple'  => false,
                		'expanded' => true
                ))
                ->add('birthday', 'text',array(
//                 		'label' =>'生日:',
                    	'required' => false,
        	        	'error_bubbling'=>true
                    ))
                ->add('tel', 'text',array(
//                 		'label' =>'手机号码:',
                    	'required' => false,
        	        	'error_bubbling'=>true
                    ))
                ->add('city', 'text',array(
                    	'required' => false,
        	        	'error_bubbling'=>true
                    ))
                ->add('education', 'text',array(
//                 		'label' =>'学历:',
                		'required' => false,
                		'error_bubbling'=>true
                ))
                ->add('profession', 'text',array(
//                 		'label' =>'职业:',
                		'required' => false,
                		'error_bubbling'=>true
                ))
                ->add('hobby', 'text',array(
//                 		'label' =>'爱好:',
                		'required' => false,
                		'error_bubbling'=>true
                ))
                ->add('personalDes', 'textarea',array(
//                 		'label' =>'个性说明:',
                		'required' => false,
                		'error_bubbling'=>true
                ));
    }
    
//     public function getDefaultOptions(array $options){
//     	return array(
//     			'validation_groups' => array('search'),

//     	);
//     }

    public function getName()
    {
        return '';
    }
}
