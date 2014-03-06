<?php

namespace Jili\EmarBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class SearchProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('keyword')
            ->add('catid', 'hidden' )
            ->add('webid','hidden' );
    }

    public function getName()
    {
        return 'search';
    }
}
