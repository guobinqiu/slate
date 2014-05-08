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
 * @Route("/admin/emarwebsitescroned",requirements={"_scheme"="https"})
 */
class AdminEmarWebsitesCronedController extends Controller
{
    /**
     * Lists all EmarWebsitesCroned entities.
     *
     * @Route("/{id}/import", name="admin_emarwebsitescroned_import")
     * @Method("GET")
     * @Template
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
        if ( ! $web) {
            $web = new EmarWebsites;
            $web->setWebId($entity->getWebId());
            $web->setWebCatid($entity->getWebCatid());
            $web->setPosition(0);
            $web->setIsHot(0);
            $web->setCommission($this->container->getParameter('emar_com.cps.action.default_rebate') );
            $em->persist($web);
            $em->flush();
        }
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
            ->getForm();
    }
}
