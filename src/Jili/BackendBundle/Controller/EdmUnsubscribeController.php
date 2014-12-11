<?php
namespace Jili\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

use Jili\ApiBundle\Entity\UserEdmUnsubscribe;

/**
 * @Route("/edm/unsubscribe",requirements={"_scheme"="https"})
 */
class EdmUnsubscribeController extends Controller  implements  IpAuthenticatedController
{

    /**
    * @Route("/list", name="_edm_unsubscribe_list")
    */
    public function listAction() {
        $email = $this->get('request')->query->get('email', '');

        $em = $this->getDoctrine()->getManager();
        $arr['userEdms'] = $em->getRepository('JiliApiBundle:UserEdmUnsubscribe')->findByEmail(trim($email));

        return $this->render('JiliBackendBundle:EdmUnsubscribe:list.html.twig', $arr);
    }

    /**
    * @Route("/add", name="_edm_unsubscribe_add_index")
    */
    public function addIndexAction() {
        //default page
        return $this->render('JiliBackendBundle:EdmUnsubscribe:add.html.twig');

    }
    /**
    * @Route("/addConfirm", name="_edm_unsubscribe_add_confirm")
    */
    public function addConfirmAction() {
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $email = $request->request->get('email');

        $return = $this->checkForAdd($email);
        if ($return['message']) {
            return $this->render('JiliBackendBundle:EdmUnsubscribe:add.html.twig', array (
                'email' => $email,
                'error_message' => $return['message']
            ));
        }

        //add UserEdmUnsubscribe
        $userEdmUnsubscribe = new UserEdmUnsubscribe();
        $userEdmUnsubscribe->setUserId($return['user_id']);
        $userEdmUnsubscribe->setCreatedTime(date_create());
        $em->persist($userEdmUnsubscribe);
        $em->flush();
        return $this->redirect($this->generateUrl('_edm_unsubscribe_list'));

    }

    public function checkForAdd($email) {
        $em = $this->getDoctrine()->getManager();

        //check email input
        if (!$email) {
            return array (
                'message' => $this->container->getParameter('reg_en_mail')
            );
        }

        //check email exist
        $user = $em->getRepository('JiliApiBundle:User')->findByEmail($email);
        if (!$user) {
            return array (
                'message' => $this->container->getParameter('chnage_no_email')
            );
        }

        //check UserEdmUnsubscribe exist
        $edm = $em->getRepository('JiliApiBundle:UserEdmUnsubscribe')->findByUserId($user[0]->getId());
        if ($edm) {
            return array (
                'message' => $this->container->getParameter('user_edm_unsubscribe_is_exist')
            );
        }

        return array (
            'message' => '',
            'user_id' => $user[0]->getId()
        );
    }
}
