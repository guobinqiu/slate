<?php

namespace Wenwen\FrontendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;

class SurveyPartnerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // 注意，禁用data参数，所有的初始值需要在entity初始化处设置
        // 这里如果设置了data参数的话，编辑页面将看不到正确的数值
        $builder
            ->add('type', 'choice', array(
                'label' => '积分类型',
                'choices'  => array(
                    'cost' => 'cost',
                    'expense' => 'expense',
                    ),
                ))
            ->add('partnerName', 'choice', array(
                'label' => 'partnerName',
                'choices'  => array(
                    'forsurvey' => 'forSurvey',
                    'triples' => 'TripleS',
                    ),
                ))
            ->add('surveyId', 'text', array(
                'label' => 'surveyId',
                'attr' => array('size' => '100'),
                ))
            ->add('url', 'url', array(
                'label' => 'url',
                'attr' => array('size' => '100'),
                ))
            ->add('title', 'text', array(
                'label' => 'title',
                'attr' => array('size' => '100'),
                ))
            ->add('content', 'textarea', array(
                'label' => 'content',
                'attr' => array('cols' => '100', 'rows' => '2'),
                'required' => false,
                ))
            ->add('reentry', 'checkbox', array(
                'label' =>'reentry',
                'required' => false,
                ))
            ->add('loi', 'text', array(
                'label' => 'loi',
                'attr' => array('size' => '5'),
                ))
            ->add('ir', 'text', array(
                'label' => 'ir',
                'attr' => array('size' => '5'),
                ))
            ->add('completePoint', 'text', array(
                'label' => 'completePoint',
                'attr' => array('size' => '5'),
                ))
            ->add('screenoutPoint', 'text', array(
                'label' => 'screenoutPoint',
                'attr' => array('size' => '5'),
                ))
            ->add('quotafullPoint', 'text', array(
                'label' => 'quotafullPoint',
                'attr' => array('size' => '5'),
                ))
            ->add('newUserOnly', 'checkbox', array(
                'label' =>'newUserOnly',
                'required' => false,
                ))
            ->add('registeredAtFrom', 'choice', array(
                'label' => '允许注册几天以后的用户参与',
                'choices'  => array(
                    '0' => '0 hour',
                    '4' => '4 hours',
                    '8' => '8 hours',
                    '12' => '12 hours',
                    '24' => '1 day (24 hours)',
                    '48' => '2 days (48 hours)',
                    '72' => '3 days (72 hours)',
                    '96' => '4 days (96 hours)',
                    '120' => '5 days (120 hours)',
                    ),
                ))
            ->add('registeredAtTo', 'choice', array(
                'label' => '允许注册几天以内的用户参与',
                'choices'  => array(
                    '0' => '0 hour',
                    '4' => '4 hours',
                    '8' => '8 hours',
                    '12' => '12 hours',
                    '24' => '1 day (24 hours)',
                    '48' => '2 days (48 hours)',
                    '72' => '3 days (72 hours)',
                    '96' => '4 days (96 hours)',
                    '120' => '5 days (120 hours)',
                    '2400000' => 'all (270 years)',
                    ),
                ))
            ->add('minAge', 'text', array(
                'label' => 'minAge',
                'attr' => array('size' => '5'),
                ))
            ->add('maxAge', 'text', array(
                'label' => 'maxAge',
                'attr' => array('size' => '5'),
                ))
            ->add('gender', 'choice', array(
                'label' => 'gender',
                'choices'  => array(
                    'both' => 'both',
                    'male' => 'male',
                    'female' => 'female',
                    ),
                ))
            ->add('province', 'text', array(
                'label' => 'province',
                'attr' => array('size' => '100'),
                'required' => false,
                ))
            ->add('city', 'text', array(
                'label' => 'city',
                'attr' => array('size' => '100'),
                'required' => false,
                ))
            ->add('status', 'field', array(
                'label' => 'status',
                'attr' => array('size' => '10'),
                ))
            //->add('save', SubmitType::class, array('label' => 'Create Post'))
            ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Wenwen\FrontendBundle\Entity\SurveyPartner',//这里可以不加，但如果是复杂的嵌套类这个地方就要显式指定
            'csrf_protection' => true,
            'intention' => 'surveypartner', //名字随便取，即使同一个用户也让这个表单的token和其它表单的token不一致，这样更加安全
            'cascade_validation' => false,//同时验证嵌套的表单
        ));
    }

    public function getName()
    {
        return 'surveypartner';
    }
}
