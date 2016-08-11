<?php

namespace Wenwen\FrontendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;

class UserIconType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('icon', 'file');
        $builder->add('x', 'hidden', array('mapped' => false));
        $builder->add('y', 'hidden', array('mapped' => false));
        $builder->add('w', 'hidden', array('mapped' => false));
        $builder->add('h', 'hidden', array('mapped' => false));
    }

    //对应表单属性: user_icon[field]
    public function getName()
    {
        return 'frontend_user_icon';
    }
}
