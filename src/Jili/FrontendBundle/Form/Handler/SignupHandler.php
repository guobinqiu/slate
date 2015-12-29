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

    private $userAgent ;
    private $remoteAddress;

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
        $em = $this->em;
        $data = $this->form->getData();
        $errors = array();
        // check exsits email
        $userByEmail = $em->getRepository('JiliApiBundle:User')->findOneByEmail($data['email']);
        if(  $userByEmail ) {
            if( empty($password)){
                $errors['email'] = $this->getParameter('reg_noal_mail'); // not activated
            } else {
                $errors['email'] = $this->getParameter('reg_al_mail'); // has been taken
            }
        }

        // check exsits nick 
        $userByNick = $em->getRepository('JiliApiBundle:User')->findNick($data['email'], $data['nickname']);
        if($userByNick) {
            $errors['nickname']= $this->container->getParameter('reg_al_nick');
        }
        return $errors;
    }
    /**
     * array('user'=> object, 'setPasswordCode'=> object) when success;
     */
    public function process()
    {
        $logger = $this->logger;
        $data = $this->form->getData();
        $em = $this->em;

        // create user
        $user = $em->getRepository('JiliApiBundle:User')->createOnSignup( array( 
            'nick'=> $data['nickname'],
            'email'=>$data['email'],
            'password'=>$data['password'],
            'user_agent' => $this->userAgent,
            'remote_address' => $this->remoteAddress,
        ));

        $setPasswordCode = $em->getRepository('JiliApiBundle:SetPasswordCode')->create(array(
            'user_id' => $user->getId()
        ));

        // sent signup activate email
        $result = $this->mailer->sendSignupActivate($user->getEmail(), $user->getNick(), $user->getId(), $setPasswordCode->getCode());

        return array( 'user'=> $user, 'setPasswordCode'=> $setPasswordCode);
    }

    /**
     * array(
     *   'user_agent'=>$request->headers->get('USER_AGENT'),
     *  'remote_address'=>$request->getClientIp()
     *   ))
     */
    public function setClientInfo(array $info) 
    {
       $this->userAgent = $info['user_agent'];
       $this->remoteAddress = $info['remote_address'];
       return $this;
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
