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
 * @Route("/admin/gmo")
 */
class AdminSurveyGmoNonBusinessController extends Controller
{
    /**
     * Lists all SurveyGmoNonBusiness entities.
     *
     * @Route("/", name="gmo")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('WenwenFrontendBundle:SurveyGmoNonBusiness')->findBy(array(), array('createdAt' => 'DESC'));

        return $this->render("WenwenFrontendBundle:admin:SurveyGmoNonBusiness/index.html.twig", array(
            'entities' => $entities,
        ));
    }

    /**
     * Displays a form to create a new SurveyGmoNonBusiness entity.
     *
     * @Route("/new", name="gmo_new")
     * @Method("GET")
     */
    public function newAction()
    {
        $form = $this->createForm(new SurveyGmoNonBusinessType());

        return $this->render("WenwenFrontendBundle:admin:SurveyGmoNonBusiness/new.html.twig", array(
            'form' => $form->createView(),
        ));
    }

    /**
     * Creates a new SurveyGmoNonBusiness entity.
     *
     * @Route("/", name="gmo_create")
     * @Method("POST")
     */
    public function createAction(Request $request)
    {
        $entity = new SurveyGmoNonBusiness();
        $form = $this->createForm(new SurveyGmoNonBusinessType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('gmo'));
        }

        return $this->render("WenwenFrontendBundle:admin:SurveyGmoNonBusiness/new.html.twig", array(
            'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing SurveyGmoNonBusiness entity.
     *
     * @Route("/{id}/edit", name="gmo_edit")
     * @Method("GET")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('WenwenFrontendBundle:SurveyGmoNonBusiness')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find SurveyGmoNonBusiness entity.');
        }

        $editForm = $this->createForm(new SurveyGmoNonBusinessType(), $entity);

        return $this->render("WenwenFrontendBundle:admin:SurveyGmoNonBusiness/edit.html.twig", array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        ));
    }

    /**
     * Edits an existing SurveyGmoNonBusiness entity.
     *
     * @Route("/{id}", name="gmo_update")
     * @Method("PUT")
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

        return $this->render("WenwenFrontendBundle:admin:SurveyGmoNonBusiness/edit.html.twig", array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        ));
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
