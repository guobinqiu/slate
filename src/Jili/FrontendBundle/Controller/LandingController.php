<?php
namespace Jili\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Jili\FrontendBundle\Form\Type\SignupType;

/**
 * @Route("/",requirements={"_scheme"="http"})
 */
class LandingController extends Controller
{
    /**
     * @Route("/external-landing",name="_landing_external", requirements={"_scheme"="https"})
     * @Method({ "GET", "POST"})
     * @Template
     */
    public function externalAction()
    {

        $request = $this->get('request');
        $logger = $this->get('logger');
        $em = $this->getDoctrine()->getManager();

        $form  = $this->createForm(new SignupType() );
        if ($request->getMethod() == 'POST'){

            $form->bind($request);
            if ($form->isValid()) {
                // the validation passed, do something with the $author object
//                $this->get('signup_activate.form_handler')->setForm($form)->setParams(array( 'user'=>$user, 'passwordToken'=>  $passwordToken ) )->process( );
                // set sucessful message flash
                $this->get('session')->getFlashBag()->add(
                    'notice',
                    '恭喜，密码设置成功！'
                );
                return $this->redirect($this->generateUrl('_user_regSuccess'));
            }
        }
        return $this->render(  'JiliFrontendBundle:Landing:external_landing.html.twig', array(
            'form'=> $form->createView()
        ));
    }

}
