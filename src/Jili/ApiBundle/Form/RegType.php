<?php

namespace Jili\ApiBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
class RegType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('attachment','file',array('required'=>false, 'error_bubbling'=>true));

    }
    
#   public function getDefaultOptions(array $options){
#       return array(
#          'data_class' => 'Jili\ApiBundle\Entity\User',
#          'csrf_protection' => true,
#          'csrf_field_name' => '_token',
#          // 一个唯一的键值来保证生成令牌
#          'intention' => 'task_item',
#       );
#   }
#

     public function setDefaultOptions(OptionsResolverInterface $resolver)
     {
         $resolver->setDefaults(array(
             'data_class'      => 'Jili\ApiBundle\Entity\User',
             'csrf_protection' => true,
             'csrf_field_name' => '_token',
             // a unique key to help generate the secret token
             'intention'       => 'task_item',
         ));
     }

    public function getName()
    {
        return '';
    }
}
