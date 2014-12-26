<?php

namespace Jili\BackendBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Doctrine\ORM\EntityRepository;


class PromotionSelfLinkProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', array(
                'label'=>'商品title 宝贝名称' ,
                'required'=> true

            ))->add( 'price','money' ,array(
                'label'=>'商品原价格',
                'required'=> true,
                'currency'=> false
            ))->add( 'pricePromotion','money', array(
                'label'=>'商品价格',
                'required'=> true,
                'currency'=> false
            ))->add( 'itemUrl','url', array(
                'required'=> false,
                'label'=> '商品url',
                'default_protocol'=>'http',
                'data'=>''
            ))->add( 'clickUrl', 'url', array(
                'label' => '自定义单品转后url',
                'required'=> true,
                'default_protocol'=>'http'
            ))->add( 'pictureName','hidden', array(
                'label'=>'图片文件名',
                'required'=> false
            ))->add( 'picture','file', array(
                'label'=>'图片上传' ,
                'mapped'=>false,
                'required'=> false
            ))->add( 'commentDescription', 'text',array(
                'label'=> '评论',
                'required'=> false,
                'data'=>''
            ))->add( 'promotionRate', 'percent',array(
                'label'=> '返利比',
                'data'=>0,
                'empty_data'=> 0
            ));

        $builder->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'onPreSetData'));
//        $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event){});
    }

    public function onPreSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $formOptions = array(
            'label'=> '商品分类',
            'class' => 'Jili\FrontendBundle\Entity\TaobaoCategory',
            'property' => 'categoryName',
            'query_builder' => function(EntityRepository $er) {
                return  $er->createQueryBuilder('tc')
                    ->Where('tc.unionProduct = :unionProduct')
                    ->andWhere('tc.deleteFlag = 0')
                    ->setParameter('unionProduct', \Jili\FrontendBundle\Entity\TaobaoCategory::SELF_PROMOTION) ;
            },
            );
        $form->add('taobaoCategory', 'entity', $formOptions);
    }

    public function getName()
    {
        return 'taobao_promotion_self_link_product';
    }
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Jili\FrontendBundle\Entity\TaobaoSelfPromotionProducts'
        ));
    }

}
