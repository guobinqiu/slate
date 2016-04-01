<?php
namespace Wenwen\FrontendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;

class SsiPartnerPermissionType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('permission_flag', 'choice', array (
            'required' => true,
            'expanded' => true,
            'multiple' => false,
            'choices' => array (
                '0'=>'否，我不愿意加入SSI市场调查活动',
                '1'=>'是，我同意并愿意完成SSI属性问卷'
            ),
             'constraints' => array (
                new Assert\NotBlank(array (
                    'message' => '请选择是否同意'
                )))
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array (
            'allow_extra_fields' => true,
            'filter_extra_fields' => false,
            'csrf_protection' => true,
            'csrf_field_name' => 'token',
            'intention' => 'task_item'
        ));
    }

    public function getName()
    {
        return 'SsiPartnerPermission';
    }
}
