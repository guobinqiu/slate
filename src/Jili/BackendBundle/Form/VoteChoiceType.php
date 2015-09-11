<?php

namespace Jili\BackendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\NotBlank;
use Jili\ApiBundle\Entity\VoteChoice;

class VoteChoiceType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('answerNumber', 'hidden', array (
            'required' => true,
            'error_bubbling' => true
        ));
        $builder->add('name', 'text', array (
            'label' => 'Choice',
            'required' => false,
            'attr' => array (
                'size' => '50'
            )
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array (
            'data_class' => 'Jili\ApiBundle\Entity\VoteChoice'
        ));
    }

    public function getName()
    {
        return 'vote_choice';
    }
}
