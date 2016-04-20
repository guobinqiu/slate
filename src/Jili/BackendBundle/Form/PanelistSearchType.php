<?php

namespace Jili\BackendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\NotBlank;
use Jili\ApiBundle\Entity\VoteChoice;

class PanelistSearchType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('user_id', 'text', array (
            'label' => 'PID',
            'required' => false,
            'attr' => array (
                'size' => '20'
            )
        ));

        $builder->add('app_mid', 'text', array (
            'label' => 'App MI',
            'required' => false,
            'attr' => array (
                'size' => '20'
            )
        ));

        $builder->add('email', 'text', array (
            'label' => 'email',
            'required' => false,
            'attr' => array (
                'size' => '30'
            )
        ));
        $builder->add('nickname', 'text', array (
            'label' => 'nickname',
            'required' => false,
            'attr' => array (
                'size' => '30'
            )
        ));
        $builder->add('mobile_number', 'text', array (
            'label' => 'mobile number',
            'required' => false,
            'attr' => array (
                'size' => '30'
            )
        ));
        $builder->add('birthday', 'text', array (
            'label' => 'birthday',
            'required' => false,
            'read_only' => 'true'
        ));

        $builder->add('registered_from', 'text', array (
            'label' => 'registered from',
            'required' => false,
            'read_only' => 'true'
        ));

        $builder->add('registered_to', 'text', array (
            'label' => 'registered to',
            'required' => false,
            'read_only' => 'true'
        ));
        $builder->add('type_registered', 'checkbox', array (
            'label' => 'registered user',
            'required' => false,
            'data' => true
        ));
        $builder->add('type_withdrawal', 'checkbox', array (
            'label' => 'withdrawal user',
            'required' => false
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array (
            'data_class' => null,
            'csrf_protection' => false,
            'csrf_field_name' => '_token',
            'intention' => 'task_item'
        ));
    }

    public function getName()
    {
        return 'panelistSerach';
    }
}
