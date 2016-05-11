<?php
namespace Wenwen\FrontendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Jili\ApiBundle\Validator\Constraints\NickRegex;

class ProfileEditType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('nick', 'text', array (
            'label' => '昵称',
            'invalid_message' => '只允许1-100个字符',
            'required' => true,
            'constraints' => array (
                new Assert\NotBlank(array (
                    'message' => '请输入昵称'
                )),
                new Assert\Length(array (
                    'min' => 1,
                    'max' => 100,
                    'minMessage' => '最少1个字符',
                    'maxMessage' => '最多100个字符'
                )),
                new NickRegex()
            )
        ));

        $builder->add('birthday', 'text', array (
            'label' => 'birthday',
            'required' => false,
            'read_only' => 'true',
            'constraints' => array (
                new Assert\Date(array (
                    'message' => '请选择正确的生日，包含年月日'
                ))
            )
        ));

        $builder->add('tel', 'text', array (
            'label' => 'telephone',
            'required' => false,
            'constraints' => array (
                new Assert\Regex(array (
                    'pattern' => "/^1\d{10}$/",
                    'message' => '您输入的手机号码格式不正确'
                ))
            )
        ));

        $builder->add('sex', 'choice', array (
            'required' => false,
            'expanded' => true,
            'multiple' => false,
            'choices' => array (
                '1' => '男',
                '2' => '女'
            )
        ));

        $builder->add('personalDes', 'textarea', array (
            'label' => 'personalDes',
            'required' => false,
            'attr' => array (
                'rows' => '6',
                'cols' => '50'
            ),
            'constraints' => array (
                new Assert\Length(array (
                    'max' => 512,
                    'maxMessage' => '不超过512个字符'
                ))
            )
        ));

        $builder->add('favMusic', 'text', array (
            'label' => 'favMusic',
            'required' => false,
            'constraints' => array (
                new Assert\Length(array (
                    'max' => 64,
                    'maxMessage' => '不超过64个字符'
                ))
            )
        ));

        $builder->add('monthlyWish', 'text', array (
            'label' => 'monthlyWish',
            'required' => false,
            'constraints' => array (
                new Assert\Length(array (
                    'max' => 64,
                    'maxMessage' => '不超过64个字符'
                ))
            )
        ));

        $builder->add('province', 'text');
        $builder->add('city', 'text');
        $builder->add('income', 'text');
        $builder->add('profession', 'text');
        $builder->add('industry_code', 'text');
        $builder->add('work_section_code', 'text');
        $builder->add('education', 'text');
        $builder->add('hobby', 'text');
        $builder->add('attachment', 'file', array (
            'required' => false,
            'error_bubbling' => true
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array (
            'data_class' => 'Jili\ApiBundle\Entity\User',
            'csrf_protection' => true,
            'csrf_field_name' => 'token',
            'intention' => 'task_item'
        ));
    }

    public function getName()
    {
        return 'profile';
    }
}