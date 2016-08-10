<?php

namespace Wenwen\FrontendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('birthday', 'text', array(
            'label' => '生日：',
            'read_only' => 'true',
        ));

        $builder->add('sex', 'choice', array(
            'label' => '性别：',
            'expanded' => true, //If set to true, radio buttons or checkboxes will be rendered (depending on the multiple value). If false, a select element will be rendered.
            'multiple' => false,
            'choices' => array(
                '1' => '男',
                '2' => '女'
            )
        ));

        $builder->add('personalDes', 'textarea', array(
            'label' => '个性签名：',
            'attr' => array(
                'rows' => '6',
                'cols' => '50'
            ),
        ));

        $builder->add('favMusic', 'text', array('label' => '喜欢的音乐：'));
        $builder->add('monthlyWish', 'text', array('label' => '本月心愿：'));
        $builder->add('province', 'text');
        $builder->add('city', 'text');

        $builder->add('income', 'choice', array(
            'label' => '月收入：',
            'empty_value' => '请选择收入',
            'choices' => array(
                '100' => '1000元以下',
                '101' => '1000-1999元',
                '102' => '2000-2999元',
                '103' => '3000-3999元',
                '104' => '4000-4999元',
                '105' => '5000-5999元',
                '106' => '6000-6999元',
                '107' => '7000-7999元',
                '108' => '8000-8999元',
                '109' => '9000-9999元',
                '110' => '10000-11999元',
                '111' => '12000-13999元',
                '112' => '14000-15999元',
                '113' => '16000-17999元',
                '114' => '18000-19999元',
                '115' => '20000-23999元',
                '116' => '24000-27999元',
                '117' => '28000-31999元',
                '118' => '32000-35999元',
                '119' => '36000元以上',
            ),
        ));

        $builder->add('profession', 'choice', array(
            'label' => '职业：',
            'empty_value' => '请选择职业',
            'choices' => array(
                '1' => '公务员',
                '2' =>  '经营管理者',
                '3' =>  '公司职员（一般事务）',
                '4' =>  '公司职员（技术人员）',
                '5' =>  '公司职员（律师，医生等专业人士）',
                '6' =>  '公司职员（其他）',
                '7' =>  '军人',
                '8' =>  '个体户',
                '9' =>  '家庭主妇',
                '10' => '打工者',
                '11' => '学生',
                '12' => '待业',
                '99' => '其他',
            )
        ));

        $builder->add('industry_code', 'choice', array(
            'label' => '行业：',
            'empty_value' => '请选择行业',
            'choices' => array(
                '1' => '农业/水产',
                '2' => '金融（银行/证券/保险）',
                '3' => '计算机/IT/数据输入',
                '4' => '电子技术/半导体/集成电路',
                '5' => '会计/审计',
                '6' => '贸易/进出口',
                '7' => '房地产',
                '8' => '印刷/出版',
                '9' => '咨询',
                '10' => '人力资源',
                '11' => '美容',
                '12' => '娱乐/休闲/体育',
                '13' => '建筑业',
                '14' => '教育',
                '15' => '工业',
                '16' => '政府机关',
                '17' => '医疗/保健',
                '18' => '法律',
                '19' => '制造业',
                '20' => '销售/市场',
                '21' => '媒体/广告/互联网',
                '22' => '制药',
                '23' => '批发/零售',
                '24' => '供电/供水/煤气/供热',
                '25' => '保安',
                '26' => '电话/通信',
                '27' => '运输/物流',
                '28' => '采掘/矿产',
                '29' => '酒店/旅游代理',
                '30' => '餐饮服务',
                '99' => '其他',
            )
        ));

        $builder->add('work_section_code', 'choice', array(
            'label' => '部门：',
            'empty_value' => '请选择部门',
            'choices' => array(
                '1' => '总务/人事/管理',
                '2' => '会计/财务',
                '3' => '销售',
                '4' => '公关/宣传',
                '5' => '调研/市场',
                '6' => '规划',
                '7' => '设计',
                '8' => '信息系统管理',
                '9' => 'IT开发',
                '10' => '技术开发',
                '11' => '生产/制造',
                '12' => '劳务保障',
                '13' => '客户服务',
                '14' => '经营',
                '15' => '法律',
                '16' => '医疗',
                '99' => '其他',
            )
        ));

        $builder->add('education', 'choice', array(
            'label' => '教育程度：',
            'empty_value' => '请选择',
            'choices' => array(
                '1' => "高中以下",
                '2' => "高中毕业",
                '3' => "大专毕业",
                '4' => "大学本科毕业",
                '5' => "研究生，博士毕业",
            )
        ));

        $builder->add('hobby', 'choice', array(
            'label' => '兴趣爱好：',
            'expanded' => true,
            'multiple' => true,
            'choices' => array(
                '1' => '上网',
                '2'=>'音乐',
                '3'=>'旅游',
                '4'=>'购物',
                '5'=>'运动',
                '6'=>'看书',
                '7'=>'游戏',
                '8'=>'娱乐',
                '9'=>'影视',
                '10'=>'动漫',
                '11'=>'时尚',
                '12'=>'艺术',
            )
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Jili\ApiBundle\Entity\UserProfile'
        ));
    }

    public function getName()
    {
        return 'user_profile';
    }
}
