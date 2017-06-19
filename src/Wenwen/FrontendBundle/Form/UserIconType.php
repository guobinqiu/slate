<?php

namespace Wenwen\FrontendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
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

    public function getName()
    {
        return 'front_user_icon';
    }
}
