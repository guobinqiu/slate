<?php

namespace Jili\BackendBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Jili\ApiBundle\Form\AdActivityType;
use Jili\ApiBundle\Entity\AdActivity;

/**
 * @abstract
 * @author: tao.jiang@d8aspring.com
 *
 * @Route("/rebate/activity",requirements={"_scheme"="https"})
 */
class RebateActivityController extends Controller  implements  IpAuthenticatedController
{
    /**
     * @Route("/caculator")
     * @Template
     */
    public function caculatorAction()
    {
        $request = $this->get('request');
        $logger= $this->get('logger');
        $em=$this->getDoctrine()->getManager();

        $f = $this->createFormBuilder( array( 'point'=> 7, 'at'=> new \Datetime('now')) )
            ->add('point', 'integer')
            ->add('at', 'datetime' , array('input'=> 'datetime' ) )
            ->getForm();

        $data_return = array();
        if( 'POST'== $request->getMethod()) {
            $f->bind($request);
            $form_data   =  $f->getData() ;
            $at =  $form_data['at'] ;

            $data_return['point_caculated'] = $this->get('rebate_point.caculator')->calcPointByCategory($form_data['point'],
                $this->container->getParameter('offerwow_com.category_type'),
                $at );

        } else {
            $at = new \Datetime('now');
        }

        $rebate = $em->getRepository('JiliApiBundle:AdActivity')->findOfMaxPercentage( $at ) ;

        if( $rebate && ! is_null($rebate['id']) ) {
            $data_return['rebate_notice'] = 'max rabate rate is <a href="'.$this->generateUrl('rebate_activity_admin_edit', array('id'=>$rebate['id'] ) ) .'">'.$rebate[1].'</a> at ' .  $at->format( 'Y-m-d H:i:s') ;
        } else {
            $data_return['rebate_notice']='There is no rabate now. <a href="'.$this->generateUrl( 'rebate_activity_admin_new')  .'">add one </a> at '. $at->format( 'Y-m-d H:i:s');
        }

        $data_return['form'] = $f->createView();
        return $data_return;
    }

    /**
     * @Route("/list", name="rebate_activity_admin_retrieve")
     * @Template
     * @abstract
     */
    public function retrieveAction()
    {
        $em = $this->getDoctrine()->getManager();
        $activities= $em->getRepository('JiliApiBundle:AdActivity')
            ->findActivities();

        return array('activities'=>$activities);
    }

    /**
     * @Route("/new", name="rebate_activity_admin_new")
     * @Template
     */
    public function createAction()
    {
        $request = $this->get('request');
        $logger = $this->get('logger');
        $form =  $this->createForm(new AdActivityType(), new AdActivity() );
        if($request->getMethod() == 'POST') {
            $form->bind($request);
            if ($form->isValid()) {
                $activity = $form->getData();
                $em = $this->getDoctrine()->getManager();
                $em->persist($activity);
                $em->flush();
                return $this->redirect($this->generateUrl('rebate_activity_admin_retrieve') );
            }
        } else {
        }
        return array( 'form'=> $form->createView() );
    }

    /**
     * @Route("/edit/{id}", name="rebate_activity_admin_edit")
     * @Template
     */
    public function editAction($id)
    {
        $request = $this->get('request');
        $logger = $this->get('logger');

        if($request->isMethod('POST')) {
            $form =  $this->createForm(new AdActivityType());
            $form->bind($request);
            if ($form->isValid()) {
                $post = $form->getData();
                $em = $this->getDoctrine()->getManager();
                $activity = $em->getRepository('JiliApiBundle:AdActivity')->findOneById( $post->getId() );
                $activity->setStartedAt($post->getStartedAt() );
                $activity->setFinishedAt($post->getFinishedAt());
                $activity->setPercentage($post->getPercentage());
                $em->persist($activity);
                $em->flush();
                return $this->redirect($this->generateUrl('rebate_activity_admin_retrieve') );
            } else {
            }
        } else {
            $em = $this->getDoctrine()->getManager();
            $activity = $em->getRepository('JiliApiBundle:AdActivity')->findOneById($id);
            $form =  $this->createForm(new AdActivityType(), $activity);
        }
        return array('form'=>$form->createView() );
    }

    /**
     * @Route("/delete/{id}", name="rebate_activity_admin_remove")
     * @Method({"POST","GET"});
     * @Template
     */
    public function removeAction($id)
    {
        $deleteForm = $this->createDeleteForm($id);
        return $this->render('JiliBackendBundle:RebateActivity:removeForm.html.twig', array('delete_form'=>$deleteForm->createView() ));
    }

    /**
     * @Route("/update/{id}", name="rebate_activity_admin_update")
     * @Method({"POST"});
     * @Template
     */
    public function updateAction($id)
    {
        $request = $this->get('request');
        if($request->getMethod() == 'POST') {
            $id = $request->request->get('id',1);
            return $this->foward('JiliApiBundle:AdminAdActivityController:edit', array('id'=> $id) );
        } else {
            return $this->foward('JiliApiBundle:AdminAdActivityController:retrieve');
        }
    }


    /**
     * Deletes a AdActivity entity.
     *
     * @Route("/{id}", name="rebate_activity_admin_delete")
     * @Method("DELETE")
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('JiliApiBundle:AdActivity')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find AdActivity entity.');
            }

            $entity->setIsDeleted( 1 );
            $em->persist($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('rebate_activity_admin_retrieve'));
    }

    /**
     * Creates a form to delete a AdActivity entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }


}
