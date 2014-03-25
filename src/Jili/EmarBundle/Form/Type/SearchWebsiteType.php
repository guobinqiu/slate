<?php

namespace Jili\EmarBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class SearchWebsiteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('q')
            ->add('rt', 'hidden' ); //router 1或null: 商品搜索(default), 2: 商家搜索
    }

    public function getName()
    {
        return 'search';
    }
}
