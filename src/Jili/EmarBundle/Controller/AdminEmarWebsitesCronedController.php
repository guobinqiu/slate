<?php

namespace Jili\EmarBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Jili\EmarBundle\Entity\EmarWebsitesCroned,
    Jili\EmarBundle\Entity\EmarWebsites;
use Jili\EmarBundle\Form\EmarWebsitesCronedType;

/**
 * EmarWebsitesCroned controller.
 *
 * @Route("/admin/emarwebsitescroned")
 */
class AdminEmarWebsitesCronedController extends Controller
{
    /**
     * Lists all EmarWebsitesCroned entities.
     *
     * @Route("/{id}/import", name="admin_emarwebsitescroned_import")
     * @Method("GET")
     * @Template()
     */
    public function importAction($id)
    {

        $logger = $this->get('logger');

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('JiliEmarBundle:EmarWebsitesCroned')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find EmarWebsitesCroned entity.');
        }

        $web =  $em->getRepository('JiliEmarBundle:EmarWebsites')->findOneByWebId( $entity->getWebId() );
        $logger->debug('{jarod}'. implode(',',array(__CLASS__, __LINE__, '') ).var_export( $web,  true) );
/**
 *        id: 9
 *            web_id: 2308
 *             web_catid: NULL
 *             commission: NULL
 *             is_deleted: 0
 *               position: 30
 *                is_hidden: 0
 *                    is_hot: 1
 *                        hot_at: 2014-04-03 11:56:37
 *                        updated_at: 2014-04-03 11:56:37
 *                        created_at: 2014-03-18 09:51:57
 *
 */
        if ( ! $web) {
            $web = new EmarWebsites;
            $web->setWebId($entity->getWebId());
            $web->setWebCatid($entity->getWebCatid());
            $web->setCommission($this->container->getParameter('emar_com.cps.action.default_rebate') );
            $em->persist($web);
            $em->flush();
        }
        //return $this->redirect($this->generateUrl('admin_emar_websites_show', array('id' => $entity->getId())));
        // insert into emar_websites & redirect to website edit page.
        return $this->redirect($this->generateUrl('admin_emar_websites_show', array('id' => $web->getId())));
    }
    /**
     * Lists all EmarWebsitesCroned entities.
     *
     * @Route("/", name="admin_emarwebsitescroned")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('JiliEmarBundle:EmarWebsitesCroned')->findAll();

        return array(
            'entities' => $entities,
        );
    }

//    /**
//     * Creates a new EmarWebsitesCroned entity.
//     *
//     * @Route("/", name="admin_emarwebsitescroned_create")
//     * @Method("POST")
//     * @Template("JiliEmarBundle:EmarWebsitesCroned:new.html.twig")
//     */
//    public function createAction(Request $request)
//    {
//        $entity  = new EmarWebsitesCroned();
//        $form = $this->createForm(new EmarWebsitesCronedType(), $entity);
//        $form->bind($request);
//
//        if ($form->isValid()) {
//            $em = $this->getDoctrine()->getManager();
//            $em->persist($entity);
//            $em->flush();
//
//            return $this->redirect($this->generateUrl('admin_emarwebsitescroned_show', array('id' => $entity->getId())));
//        }
//
//        return array(
//            'entity' => $entity,
//            'form'   => $form->createView(),
//        );
//    }
//
//    /**
//     * Displays a form to create a new EmarWebsitesCroned entity.
//     *
//     * @Route("/new", name="admin_emarwebsitescroned_new")
//     * @Method("GET")
//     * @Template()
//     */
//    public function newAction()
//    {
//        $entity = new EmarWebsitesCroned();
//        $form   = $this->createForm(new EmarWebsitesCronedType(), $entity);
//
//        return array(
//            'entity' => $entity,
//            'form'   => $form->createView(),
//        );
//    }
//
    /**
     * Finds and displays a EmarWebsitesCroned entity.
     *
     * @Route("/{id}", name="admin_emarwebsitescroned_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('JiliEmarBundle:EmarWebsitesCroned')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find EmarWebsitesCroned entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

//    /**
//     * Displays a form to edit an existing EmarWebsitesCroned entity.
//     *
//     * @Route("/{id}/edit", name="admin_emarwebsitescroned_edit")
//     * @Method("GET")
//     * @Template()
//     */
//    public function editAction($id)
//    {
//        $em = $this->getDoctrine()->getManager();
//
//        $entity = $em->getRepository('JiliEmarBundle:EmarWebsitesCroned')->find($id);
//
//        if (!$entity) {
//            throw $this->createNotFoundException('Unable to find EmarWebsitesCroned entity.');
//        }
//
//        $editForm = $this->createForm(new EmarWebsitesCronedType(), $entity);
//        $deleteForm = $this->createDeleteForm($id);
//
//        return array(
//            'entity'      => $entity,
//            'edit_form'   => $editForm->createView(),
//            'delete_form' => $deleteForm->createView(),
//        );
//    }

//    /**
//     * Edits an existing EmarWebsitesCroned entity.
//     *
//     * @Route("/{id}", name="admin_emarwebsitescroned_update")
//     * @Method("PUT")
//     * @Template("JiliEmarBundle:EmarWebsitesCroned:edit.html.twig")
//     */
//    public function updateAction(Request $request, $id)
//    {
//        $em = $this->getDoctrine()->getManager();
//
//        $entity = $em->getRepository('JiliEmarBundle:EmarWebsitesCroned')->find($id);
//
//        if (!$entity) {
//            throw $this->createNotFoundException('Unable to find EmarWebsitesCroned entity.');
//        }
//
//        $deleteForm = $this->createDeleteForm($id);
//        $editForm = $this->createForm(new EmarWebsitesCronedType(), $entity);
//        $editForm->bind($request);
//
//        if ($editForm->isValid()) {
//            $em->persist($entity);
//            $em->flush();
//
//            return $this->redirect($this->generateUrl('admin_emarwebsitescroned_edit', array('id' => $id)));
//        }
//
//        return array(
//            'entity'      => $entity,
//            'edit_form'   => $editForm->createView(),
//            'delete_form' => $deleteForm->createView(),
//        );
//    }

//    /**
//     * Deletes a EmarWebsitesCroned entity.
//     *
//     * @Route("/{id}", name="admin_emarwebsitescroned_delete")
//     * @Method("DELETE")
//     */
//    public function deleteAction(Request $request, $id)
//    {
//        $form = $this->createDeleteForm($id);
//        $form->bind($request);
//
//        if ($form->isValid()) {
//            $em = $this->getDoctrine()->getManager();
//            $entity = $em->getRepository('JiliEmarBundle:EmarWebsitesCroned')->find($id);
//
//            if (!$entity) {
//                throw $this->createNotFoundException('Unable to find EmarWebsitesCroned entity.');
//            }
//
//            $em->remove($entity);
//            $em->flush();
//        }
//
//        return $this->redirect($this->generateUrl('admin_emarwebsitescroned'));
//    }

    /**
     * Creates a form to delete a EmarWebsitesCroned entity by id.
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
