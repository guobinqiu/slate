<?php
namespace Jili\FrontendBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;

class GameEggsBreakerTaoBaoOrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('orderId', 'text', array(
            'label'=>'订单号',
            'invalid_message' => '订单号不正确',
        ))->add('orderPaid', 'money', array(
            'divisor' => 100,
            'label'=> '支付金额'
        ));
    }

    public function getName()
    {
        return 'order';
    }
}
?>
