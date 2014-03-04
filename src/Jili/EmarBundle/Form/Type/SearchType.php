<?php

namespace Jili\EmarBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('keyword');
    }

    public function getName()
    {
        return 'search';
    }
}
