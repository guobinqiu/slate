<?php

namespace Wenwen\FrontendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('nick', 'text');

        $builder->add('tel', 'text', array(
            'constraints' => array(
                new Assert\NotBlank(array('message' => '请输入您的手机号码'))
            )
        ));

        //对应user对象的userProfile属性
        $builder->add('userProfile', new UserProfileType());
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Wenwen\FrontendBundle\Entity\User',
            'cascade_validation' => true,//同时验证嵌套的表单
        ));
    }

    public function getName()
    {
        return 'front_user';
    }
}
