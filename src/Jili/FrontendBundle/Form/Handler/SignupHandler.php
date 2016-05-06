<?php
namespace Jili\FrontendBundle\Form\Handler;


use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Form\FormInterface;
use Doctrine\ORM\EntityManager;
use Jili\ApiBundle\Utility\PasswordEncoder;

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

    private $password_salt;
    private $password_encrypt_type;

    public function __construct( $crypt_method,$salt)
    {
        $this->password_crypt_type = $crypt_method;
        $this->password_salt = $salt;
    }

    public function setForm(FormInterface $form)
    {
        $this->form = $form;
        return $this;
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
            'createdUserAgent' => $this->userAgent,
            'createdRemoteAddr' => $this->remoteAddress,
        ));

        $setPasswordCode = $em->getRepository('JiliApiBundle:SetPasswordCode')->create(array(
            'user_id' => $user->getId()
        ));

        $password = PasswordEncoder::encode($this->password_crypt_type,$data['password'] , $this->password_salt);;


        $em->getRepository('JiliApiBundle:UserWenwenLogin')->createOne(array('user_id'=> $user->getId() ,
            'password' => $password,
            'crypt_type' => $this->password_crypt_type ,
            'salt'=> $this->password_salt ));

        if( false === $data['unsubscribe'] ) {
            $em->getRepository('JiliApiBundle:UserEdmUnsubscribe')
                ->insertOne( $user->getId());
        }


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


}
