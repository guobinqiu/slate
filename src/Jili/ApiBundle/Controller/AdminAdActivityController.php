<?php
namespace Jili\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Jili\ApiBundle\Form\AdActivityType;
use Jili\ApiBundle\Entity\AdActivity;

/**
 * @abstract
 * @author: jiangtao@voyagegroup.com
 *
 */
class AdminAdActivityController extends Controller
{
    
    /**
     * @Route("/list", name="_admin_adActivity_retrieve")
     * @Template
     * @abstract
     */
    public function retrieveAction(){
        $em = $this->getDoctrine()->getManager();
        $activities= $em->getRepository('JiliApiBundle:AdActivity')
            ->findActivities();

        return array('activities'=>$activities);
    }

    /**
     * @Route("/new", name="_admin_adActivity_new")
     * @Template
     */
    public function createAction(){
        $request = $this->get('request');
        $logger = $this->get('logger');
        $form =  $this->createForm(new AdActivityType(), new AdActivity() );
        $logger->debug('{jarod}'. __FILE__.':'.__LINE__. var_export( $request->getMethod(), true));
        if($request->getMethod() == 'POST') {
            $form->bind($request);
            if ($form->isValid()) {
                $activity = $form->getData();
                $em = $this->getDoctrine()->getManager();
                $em->persist($activity);
                $em->flush();
                return $this->redirect($this->generateUrl('_admin_adActivity_retrieve') );
            }
        }
        return array( 'form'=> $form->createView() );
    }

    /**
     * @Route("/edit/{id}", name="_admin_adActivity_edit")
     * @Template
     */
    public function editAction($id){
        $request = $this->get('request');
        $logger = $this->get('logger');

        if($request->isMethod('POST')) { 
            $form =  $this->createForm(new AdActivityType());
            $form->bind($request);
            if ($form->isValid()) {

                $logger->debug('{jarod}'. __FILE__.':'.__LINE__. var_export( $request->request->all(), true));
                $logger->debug('{jarod}'. __FILE__.':'.__LINE__. var_export( $form->getData(), true));


                $post = $form->getData(); 
                $em = $this->getDoctrine()->getManager();
                $activity = $em->getRepository('JiliApiBundle:AdActivity')->findOneById( $post->getId() );
                $activity->setStartedAt($post->getStartedAt() );
                $activity->setFinishedAt($post->getFinishedAt());
                $activity->setPercentage($post->getPercentage());
                $em->persist($activity);
                $em->flush();
                return $this->redirect($this->generateUrl('_admin_adActivity_retrieve') );
            } else {
                $logger->debug('{jarod}'. __FILE__.':'.__LINE__.' invalid form post') ;
            }
        } else {
            $em = $this->getDoctrine()->getManager();
            $activity = $em->getRepository('JiliApiBundle:AdActivity')->findOneById($id);
            $form =  $this->createForm(new AdActivityType(), $activity);
        }
        return array('form'=>$form->createView() );
    }

    /**
     * @Route("/delete/{id}", name="_admin_adActivity_remove")
     * @Method({"POST","GET"});
     * @Template
     */
    public function removeAction($id) {
        $deleteForm = $this->createDeleteForm($id);
        return $this->render('JiliApiBundle:AdminAdActivity:removeForm.html.twig', array('delete_form'=>$deleteForm->createView() ));
    }

    /**
     * @Route("/update/{id}", name="_admin_adActivity_update")
     * @Method({"POST"});
     * @Template
     */
    public function updateAction($id){
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
     * @Route("/{id}", name="activity_admin_delete")
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

#$repository = $em->getRepository('JiliApiBundle:AdActivity' );
#$cn = get_class($repository);
#$cm = get_class_methods($cn);
#$logger = $this->get('logger');
#$logger->debug('{jarod}'. __FILE__.':'.__LINE__. var_export($cm, true) );

            $entity->setIsDeleted( 1 );
            $em->persist($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('_admin_adActivity_retrieve'));
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
?>
