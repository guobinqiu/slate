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

        $form = $this->createForm(new SignupType() );

        $templ_vars = array();
        if ($request->getMethod() == 'POST'){

            #$session=$this->get('session');
            #$capcha_keys = $session->get('captcha_whitelist_key');
            #$capcha_expected = $session->get($capcha_keys[0]);
            #$logger->debug('{jarod}'. implode( ':', array(__LINE__, __FILE__,'') ). var_export( $capcha_expected, true) );

            $logger->debug('{jarod}'. implode( ':', array(__LINE__, __FILE__,'') ). var_export( $request->request->all(), true) );
            $form->bind($request);
#            $cn = get_class($form);
#            $cm = get_class_methods($cn);
#            $logger->debug('{jarod}'. implode( ':', array(__LINE__, __FILE__,'') ). var_export( $cm, true) );
#            $logger->debug('{jarod}'. implode( ':', array(__LINE__, __FILE__,'') ). var_export( $cn, true) );
#            $logger->debug('{jarod}'. implode( ':', array(__LINE__, __FILE__,'') ). var_export( $form->getData() , true) );
            if ($form->isValid()) {
                // the validation passed, do something with the $author object
                $form_handler = $this->get('signup.form_handler');
                $form_handler->setForm($form);
                $errors =  $form_handler->validate();

                if( empty( $errors) ) {
                    $result =  $form_handler->process( );
                    $logger->debug('{jarod}'. implode( ':', array(__LINE__, __FILE__,'$result','') ). var_export( $result , true) );
                    $user = $result['user'];
                    $passwordCode = $result['setPasswordCode'];

                    // set sucessful message flash
                    $this->get('session')->getFlashBag()->add(
                        'notice',
                        '恭喜，注册成功！'
                    );
                    return $this->redirect($this->generateUrl('_user_checkReg', array('id'=>$user->getId()),true));
                }
                $templ_vars ['error'] = $errors ;
            } else {
                $errors = $form->getErrors();
                $logger->debug('{jarod}'. implode( ':', array(__LINE__, __FILE__,'') ). var_export( $errors, true) );
            }
        }
        $templ_vars['form'] =  $form->createView();
        return $this->render(  'JiliFrontendBundle:Landing:external_landing_ii.html.twig',$templ_vars);
    }
}
