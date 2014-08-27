<?php
namespace Jili\FrontendBundle\Form\Handler;


use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Form\FormInterface;
use Doctrine\ORM\EntityManager;

use Jili\FrontendBundle\Mailer\Mailer;

/**
 *
 **/
class SignupHandler
{

    private $em;
    private $logger;
    private $session;
    private $mailer;

    private $form;
    private $params;

    public function setForm(FormInterface $form)
    {
        $this->form = $form;
        return $this;
    }

    /**
     * @return array('error'=> THE_MESSAGE) when error 
     */
    public function validate()
    {
        $errors = array();
        // check exsits email

        // check exsits nick 

        return $errors;
    }
    /**
     * array('user'=> object, 'setPasswordCode'=> object) when success;
     */
    public function process()
    {

        $form = $this->form;
        $logger = $this->logger;
        $data = $form->getData();
        $logger->debug('{jarod}'.implode( ':', array(__LINE__, __CLASS__) ).  var_export( $data, true) );

        $em = $this->em;

        // create user
        $user = $em->getRepository('JiliApiBundle:User')->createOnSignup( array( 
            'nick'=> $data['nickname'],
            'email'=>$data['email']
        ));

        $setPasswordCode = $em->getRepository('JiliApiBundle:SetPasswordCode')->create(array(
            'user_id' => $user->getId()
        ));

        // sent signup activate email
        $this->mailer->sendSignupActivate($user->getEmail(), $user->getNick(), $user->getId(), $setPasswordCode->getCode() );

        return array( 'user'=> $user, 'setPasswordCode'=> $setPasswordCode);
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function setEntityManager(EntityManager $em)
    {
        $this->em = $em;
    }

    public function setSession(Session $sess)
    {
        $this->session  = $sess;
    }


    public function setContainer($container)
    {
        $this->container = $container;
    }

    private function getParameter($key)
    {
        return $this->container->getParameter($key);
    }

    public function setMailer(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }
}
