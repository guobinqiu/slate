<?php

namespace Jili\EmarBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Jili\EmarBundle\Entity\EmarWebsites;
use Jili\EmarBundle\Form\EmarWebsitesType;

/**
 * EmarWebsites controller.
 *
 * @Route("/admin/websites")
 */
class AdminWebsitesController extends Controller
{
    /**
     * Lists all EmarWebsites entities.
     *
     * @Route("/", name="admin_emar_websites")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('JiliEmarBundle:EmarWebsites')->findBy( array('isDeleted'=> false ) );

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Creates a new EmarWebsites entity.
     *
     * @Route("/", name="admin_emar_websites_create")
     * @Method("POST")
     * @Template("JiliEmarBundle:EmarWebsites:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new EmarWebsites();
        $form = $this->createForm(new EmarWebsitesType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_emar_websites_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new EmarWebsites entity.
     *
     * @Route("/new", name="admin_emar_websites_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new EmarWebsites();
        $form   = $this->createForm(new EmarWebsitesType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a EmarWebsites entity.
     *
     * @Route("/{id}", name="admin_emar_websites_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('JiliEmarBundle:EmarWebsites')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find EmarWebsites entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * finds and displays a emarwebsites entity.
     *
     * @route("/detail/{wid}", name="admin_emar_websites_detail")
     * @method("get")
     * @template()
     */
    public function detailAction($wid)
    {
        $logger = $this->get('logger');
        #$logger->debug('{jarod}'. implode(':', array(__LINE__, __CLASS__,'')));
        $detail = $this->get('website.detail_get')->fetch(array('webid'=>$wid) );
        #$logger->debug('{jarod}'. implode(':', array(__LINE__, __CLASS__,'')). var_export( $detail, true) );
        return compact('detail');
    }

    /**
     * Displays a form to edit an existing EmarWebsites entity.
     *
     * @Route("/{id}/edit", name="admin_emar_websites_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('JiliEmarBundle:EmarWebsites')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find EmarWebsites entity.');
        }

        $editForm = $this->createForm(new EmarWebsitesType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render( 'JiliEmarBundle:AdminWebsites:edit.html.twig',array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing EmarWebsites entity.
     *
     * @Route("/{id}", name="admin_emar_websites_update")
     * @Method("PUT")
     * @Template("JiliEmarBundle:AdminWebsites:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $logger = $this->get('logger');
        $entity = $em->getRepository('JiliEmarBundle:EmarWebsites')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find EmarWebsites entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new EmarWebsitesType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {

#         $logger->debug('{jarod}'. implode(':', array(__LINE__, __CLASS__,'')) );
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_emar_websites_edit', array('id' => $id)));
        } else {

#         $logger->debug('{jarod}'. implode(':', array(__LINE__, __CLASS__,'')) );
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a EmarWebsites entity.
     *
     * @Route("/{id}", name="admin_emar_websites_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('JiliEmarBundle:EmarWebsites')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find EmarWebsites entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('admin_emar_websites'));
    }

    /**
     * Creates a form to delete a EmarWebsites entity by id.
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
