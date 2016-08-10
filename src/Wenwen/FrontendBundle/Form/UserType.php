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
        $builder->add('nick', 'text', array('label' => '昵称：'));

        $builder->add('tel', 'text', array(
            'label' => '手机号码：',
            'constraints' => array(
                new Assert\NotBlank()
            )
        ));

        //对应user对象的userProfile属性
        $builder->add('userProfile', new UserProfileType());
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Jili\ApiBundle\Entity\User',
            'cascade_validation' => true,//同时验证嵌套的表单
        ));
    }

    //对应表单属性: user[field]
    public function getName()
    {
        return 'user';
    }
}
