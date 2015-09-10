<?php

namespace Jili\BackendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\NotBlank;
use Jili\ApiBundle\Entity\VoteChoice;

class VoteType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('id', 'hidden', array (
            'required' => true,
            'error_bubbling' => true
        ));
        $builder->add('startTime', 'text', array (
            'label' => 'startTime',
            'required' => true,
            'read_only' => 'true',
            'data' => date('Y-m-d'),
            'constraints' => new NotBlank(),
            'invalid_message' => 'Invalid startTime'
        ));
        $builder->add('endTime', 'text', array (
            'label' => 'endTime',
            'required' => true,
            'read_only' => 'true',
            'data' => date('Y-m-d'),
            'constraints' => new NotBlank(),
            'invalid_message' => 'Invalid endTime'
        ));
        $builder->add('pointValue', 'number', array (
            'label' => 'pointValue',
            'required' => true,
            'data' => 1,
            'constraints' => array (
                new NotBlank(),
                new Assert\Range(array (
                    'min' => 0,
                    'max' => 1,
                    'minMessage' => 'Invalid pointValue',
                    'maxMessage' => 'Invalid pointValue'
                ))
            ),
            'invalid_message' => 'Invalid pointValue'
        ));
        $builder->add('title', 'text', array (
            'label' => 'title',
            'required' => true,
            'data' => 'test:title',
            'attr' => array (
                'size' => '50'
            ),
            'constraints' => new NotBlank(),
            'invalid_message' => 'Invalid title'
        ));
        $builder->add('description', 'textarea', array (
            'label' => 'description',
            'required' => true,
            'data' => 'test:description
test:description',
            'attr' => array (
                'rows' => '6',
                'cols' => '50'
            ),
            'constraints' => new NotBlank(),
            'invalid_message' => 'Invalid description'
        ));

        $builder->add('voteChoices', 'collection', array (
            'type' => new VoteChoiceType()
        ));

        $builder->add('voteImage', 'file', array (
            'required' => false,
            'label' => 'voteImage',
            'constraints' => array (
                new Assert\Image()
            ),
            'invalid_message' => 'Invalid voteImage'
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array (
            'data_class' => 'Jili\ApiBundle\Entity\Vote',
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            // a unique key to help generate the secret token
            'intention' => 'task_item'
        ));
    }

    public function getName()
    {
        return 'vote';
    }
}
