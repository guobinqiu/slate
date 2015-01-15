<?php

namespace Jili\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\HttpFoundation\Request;
use Jili\BackendBundle\Form\Type\ActivityGathering\OrderTotalType;

/**
 * @Route("/admin/activity/gathering", requirements={"_scheme" = "https"})
 */
class ActivityGatheringController extends Controller implements IpAuthenticatedController 
{

    /**
     * @Route("/order-total/get")
     * @Method("GET")
     */
    function getOrderTotalAction()
    {
        $total = $this->get('month_activity.gathering')->getTotal();
        return $this->render('JiliBackendBundle:ActivityGathering:taobao_order_total_index.html.twig', array(
            'total'=> $total
        ));
    }

    /**
     * @Route("/order-total/create")
     * @Method("GET")
     */
    function createOrderTotalAction()
    {
        // clear exists data for create new one.
        $form = $this->createForm(new OrderTotalType());
        return $this->render('JiliBackendBundle:ActivityGathering:create_taobao_order_total_form.html.twig', array(
            'form'=>$form->createView()
        ));
    }

    /**
     * @Route("/order-total/edit")
     * @Method("GET")
     */
    function editOrderTotalAction()
    {
        // read exists data
        $total = $this->get('month_activity.gathering')->getTotal();
        if( is_null($total)) {
            $this->get('session')->getFlashBag()->add('error', '还没有新建');
            return $this->redirect($this->generateUrl('jili_backend_activitygathering_editordertotal'));

        }

        //显示 edit form
        $form = $this->createForm(new OrderTotalType(), array('total'=> $total, 'total_current'=> $total));
        return $this->render('JiliBackendBundle:ActivityGathering:edit_taobao_order_total_form.html.twig', array(
            'form'=>$form->createView()
        ));
    }

    /**
     * @Route("/order-total/update")
     * @Method("POST")
     */
    function updateOrderTotalAction(Request $request)
    {
        $form = $this->createForm(new OrderTotalType());
        $form->bind($request);
        if ($form->isValid()) {
            $data = $form->getData();
            $total = $this->get('month_activity.gathering')->getTotal();
            if( $total !== $data['total_current'] ) {
                $this->get('month_activity.gathering')->updateTotal($data['total']);
                $this->get('session')->getFlashBag()->add('notice', '更新成功');
                return $this->redirect($this->generateUrl('jili_backend_activitygathering_getordertotal'));
            }
        }

        return $this->render('JiliBackendBundle:ActivityGathering:edit_taobao_order_total_form.html.twig', array(
            'form'=>$form->createView()
        ));

    }

    /**
     * @Route("/order-total/save")
     * @Method("POST")
     */
    function saveOrderTotalAction(Request $request)
    {
        $form = $this->createForm(new OrderTotalType());
        $form->bind($request);

        if ($form->isValid()) {
            $data = $form->getData();
            $this->get('month_activity.gathering')->createTotal($data['total']);
            $this->get('session')->getFlashBag()->add('notice', '创建成功');
            return $this->redirect($this->generateUrl('jili_backend_activitygathering_getordertotal'));
            // GatheringService->createTotal()
        }

        return $this->render('JiliBackendBundle:ActivityGathering:create_taobao_order_total_form.html.twig', array(
            'form'=>$form->createView()
        ));
    }
}

