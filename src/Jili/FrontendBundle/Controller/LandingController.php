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
        $session = $this->get('session');
        if($session->has('uid')) {
            return $this->redirect( $this->generateUrl('_homepage'));
        }

        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(new SignupType() );
        $templ_vars = array();
        if ($request->getMethod() === 'POST') {
            $form->bind($request);
            if ($form->isValid()) {
                // the validation passed, do something with the $author object
                $form_handler = $this->get('signup.form_handler');
                $form_handler->setForm($form);
                $errors =  $form_handler->validate();
                if( empty( $errors) ) {
                    $result =  $form_handler->process();
                    $user = $result['user'];
                    $this->get('user_sign_up_route.listener')->signed(array('user_id'=> $user->getId() ) );
                    // set sucessful message flash
                    $this->get('session')->getFlashBag()->add('notice','恭喜，注册成功！');
                    return $this->redirect($this->generateUrl('_user_checkReg', array('id'=>$user->getId()),true));
                }
                $templ_vars ['errors'] = $errors ;
            }
        } else if ($request->getMethod() === 'GET' ) {

            if( $request->query->has('spm') ) {
                $query['spm'] =  $request->query->get('spm');
                $this->get('user_sign_up_route.listener')->refreshRouteSession( array('spm'=> $request->get('spm', null) ) );
            }
            $this->get('user_sign_up_route.listener')->log();
            $logger->debug('{jarod}'. implode( ':', array(__LINE__, __FILE__,'') ). var_export( $request->getMethod() , true) );
        }
        $templ_vars['form'] =  $form->createView();
        return $this->render( 'JiliFrontendBundle:Landing:external_landing.html.twig', $templ_vars);
    }
}
