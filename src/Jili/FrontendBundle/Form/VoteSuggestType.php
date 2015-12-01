<?php

namespace Jili\FrontendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Jili\ApiBundle\Entity\VoteChoice;

class VoteSuggestType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', 'text', array (
            'label' => '标题：',
            'required' => true,
            'max_length' => 30,
            'constraints' => array (
                new NotBlank(array (
                    'message' => '↑请输入快速问答的标题'
                )),
                new Length(array (
                    'max' => 30,
                    'maxMessage' => '↑您输入的标题文字太长'
                ))
            )
        ));
        $builder->add('description', 'textarea', array (
            'label' => '相关说明及描述：',
            'required' => true,
            'max_length' => 200,
            'constraints' => array (
                new NotBlank(array (
                    'message' => '↑请输入该主题的相关说明及描述'
                )),
                new Length(array (
                    'max' => 200,
                    'maxMessage' => '↑您输入的主题描述文字太长'
                ))
            )
        ));
        $builder->add('option1', 'text', array (
            'label' => '选项1：',
            'required' => true,
            'max_length' => 30,
            'constraints' => array (
                new NotBlank(array (
                    'message' => '↑请输入选项1'
                )),
                new Length(array (
                    'max' => 30,
                    'maxMessage' => '↑您输入的选项文字太长'
                ))
            )
        ));
        $builder->add('option2', 'text', array (
            'label' => '选项2：',
            'required' => true,
            'max_length' => 30,
            'constraints' => array (
                new NotBlank(array (
                    'message' => '↑请输入选项2'
                )),
                new Length(array (
                    'max' => 30,
                    'maxMessage' => '↑您输入的选项文字太长'
                ))
            )
        ));
        $builder->add('option3', 'text', array (
            'label' => '选项3：',
            'required' => true,
            'max_length' => 30,
            'constraints' => array (
                new NotBlank(array (
                    'message' => '↑请输入选项3'
                )),
                new Length(array (
                    'max' => 30,
                    'maxMessage' => '↑您输入的选项文字太长'
                ))
            )
        ));
        $builder->add('option4', 'text', array (
            'label' => '选项4：',
            'required' => false,
            'max_length' => 30,
            'constraints' => array (
                new Length(array (
                    'max' => 30,
                    'maxMessage' => '↑您输入的选项文字太长'
                ))
            )
        ));
        $builder->add('option5', 'text', array (
            'label' => '选项5：',
            'required' => false,
            'max_length' => 30,
            'constraints' => array (
                new Length(array (
                    'max' => 30,
                    'maxMessage' => '↑您输入的选项文字太长'
                ))
            )
        ));
        $builder->add('option6', 'text', array (
            'label' => '选项6：',
            'required' => false,
            'max_length' => 30,
            'constraints' => array (
                new Length(array (
                    'max' => 30,
                    'maxMessage' => '↑您输入的选项文字太长'
                ))
            )
        ));
        $builder->add('option7', 'text', array (
            'label' => '选项7：',
            'required' => false,
            'max_length' => 30,
            'constraints' => array (
                new Length(array (
                    'max' => 30,
                    'maxMessage' => '↑您输入的选项文字太长'
                ))
            )
        ));
        $builder->add('option8', 'text', array (
            'label' => '选项8：',
            'required' => false,
            'max_length' => 30,
            'constraints' => array (
                new Length(array (
                    'max' => 30,
                    'maxMessage' => '↑您输入的选项文字太长'
                ))
            )
        ));
        $builder->add('option9', 'text', array (
            'label' => '选项9：',
            'required' => false,
            'max_length' => 30,
            'constraints' => array (
                new Length(array (
                    'max' => 30,
                    'maxMessage' => '↑您输入的选项文字太长'
                ))
            )
        ));
        $builder->add('option10', 'text', array (
            'label' => '选项10：',
            'required' => false,
            'max_length' => 30,
            'constraints' => array (
                new Length(array (
                    'max' => 30,
                    'maxMessage' => '↑您输入的选项文字太长'
                ))
            )
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array (
            'data_class' => null,
            'csrf_protection' => true,
            'csrf_field_name' => 'token',
            // a unique key to help generate the secret token
            'intention' => 'task_item'
        ));
    }

    public function getName()
    {
        return 'voteSuggest';
    }
}
