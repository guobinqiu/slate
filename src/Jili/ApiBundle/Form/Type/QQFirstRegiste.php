<?php

namespace Jili\ApiBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class QQFirstRegiste extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('email_id', 'text',array(
                        'required' => true,
                        'error_bubbling'=> false
                ));
    }
    
    public function getName()
    {
        return '';
    }
}
