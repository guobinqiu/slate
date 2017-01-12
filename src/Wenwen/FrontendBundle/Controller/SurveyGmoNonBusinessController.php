<?php

namespace Wenwen\FrontendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Wenwen\FrontendBundle\Entity\SurveyGmoNonBusiness;
use Wenwen\FrontendBundle\Form\SurveyGmoNonBusinessType;

/**
 * SurveyGmoNonBusiness controller.
 *
 * @Route("/gmo")
 */
class SurveyGmoNonBusinessController extends Controller
{
    /**
     * Lists all SurveyGmoNonBusiness entities.
     *
     * @Route("/", name="gmo")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('WenwenFrontendBundle:SurveyGmoNonBusiness')->findBy(array(), array('createdAt' => 'DESC'));

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Creates a new SurveyGmoNonBusiness entity.
     *
     * @Route("/", name="gmo_create")
     * @Method("POST")
     * @Template("WenwenFrontendBundle:SurveyGmoNonBusiness:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new SurveyGmoNonBusiness();
        $form = $this->createForm(new SurveyGmoNonBusinessType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('gmo'));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new SurveyGmoNonBusiness entity.
     *
     * @Route("/new", name="gmo_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new SurveyGmoNonBusiness();
        $form   = $this->createForm(new SurveyGmoNonBusinessType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing SurveyGmoNonBusiness entity.
     *
     * @Route("/{id}/edit", name="gmo_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('WenwenFrontendBundle:SurveyGmoNonBusiness')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find SurveyGmoNonBusiness entity.');
        }

        $editForm = $this->createForm(new SurveyGmoNonBusinessType(), $entity);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        );
    }

    /**
     * Edits an existing SurveyGmoNonBusiness entity.
     *
     * @Route("/{id}", name="gmo_update")
     * @Method("PUT")
     * @Template("WenwenFrontendBundle:SurveyGmoNonBusiness:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('WenwenFrontendBundle:SurveyGmoNonBusiness')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find SurveyGmoNonBusiness entity.');
        }

        $editForm = $this->createForm(new SurveyGmoNonBusinessType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('gmo'));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        );
    }

    /**
     * Deletes a SurveyGmoNonBusiness entity.
     *
     * @Route("/", name="gmo_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request)
    {
        $id = $request->request->get('id');
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('WenwenFrontendBundle:SurveyGmoNonBusiness')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find SurveyGmoNonBusiness entity.');
        }

        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('gmo'));
    }
}
