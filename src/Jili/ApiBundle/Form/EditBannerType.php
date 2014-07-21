<?php

namespace Jili\ApiBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class EditBannerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('attachment','file',array(
                        'required'=>false,
                        'error_bubbling'=>true,
                        ));
    }
    public function getName()
    {
        return '';
    }
}
