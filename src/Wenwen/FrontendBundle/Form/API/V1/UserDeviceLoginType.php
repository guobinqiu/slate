<?php

namespace Wenwen\FrontendBundle\Form\API\V1;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Wenwen\FrontendBundle\Model\OwnerType;

class UserDeviceLoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('device_id', 'text', array(
                'constraints' => new Assert\NotBlank(),
            ))
            ->add('device_type', 'text', array(
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Choice(array("ios", "android", "browser")),
                ),
            ))
            ->add('recruit_route', 'text')
            ->add('invitation_code', 'text')
            ->add('os_version', 'text')
            ->add('client_ip', 'text')
            ->add('owner_type', 'text', array(
                'constraints' => new Assert\Choice(array(OwnerType::DATASPRING, OwnerType::INTAGE, OwnerType::ORGANIC))
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection' => false
        ));
    }

    public function getName()
    {
        return 'login';
    }
}
