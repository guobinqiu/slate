<?php
namespace Jili\ApiBundle\Form\Handler;


use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Form\FormInterface;
use Doctrine\ORM\EntityManager;

/**
 *
 **/
class SignupActivateHandler
{

    private $em;
    private $logger;
    private $session;

    private $form;
    private $params;

    public function setForm(FormInterface $form)
    {
        $this->form = $form;
        return $this;
    }
    public function setParams($params ) 
    {
        $this->params = $params;
        return $this;
    }
    /**
     */
    public function process()
    {
        extract($this->params);

        $form = $this->form;
        $logger = $this->logger;
        $data = $form->getData();
        if($data['agreement']) {
                $this->login_listener->checkNewbie($user);
                $user->setPwd($data['password']);
                $user->setLastLoginDate(date_create(date('Y-m-d H:i:s')));
                $user->setLastLoginIp($this->container->get('request')->getClientIp());
                $passwordToken->setIsAvailable($this->getParameter('init'));

                $em=$this->em;
                $em->persist($user);
                $em->persist($passwordToken);
                $em->flush();

                //设置密码之后，注册成功，发邮件2014-01-10
                $soapMailLister = $this->soap_mail_listener;
                $soapMailLister->setCampaignId($this->getParameter('register_success_campaign_id')); //活动id
                $soapMailLister->setMailingId($this->getParameter('register_success_mailing_id')); //邮件id
                $soapMailLister->setGroup(array ('name' => '积粒网','is_test' => 'false')); //group

                $recipient_arr = array (
                    array (
                        'name' => 'email',
                        'value' => $user->getEmail()
                    )
                );
                $soapMailLister->sendSingleMailing($recipient_arr);

                $this->login_listener->initSession($user);
                // The user was insert when regAction
                $this->login_listener->log($user);


        } else {
            // check the agreement
        }
        $logger->debug('{jarod}'.implode( ':', array(__LINE__, __CLASS__) ).  var_export( $data, true) );
        return true;
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

    public function setLoginListener($login_listener)
    {
        $this->login_listener = $login_listener;
    }

    public function setSoapMailListener($soap_mail_listener)
    {
        $this->soap_mail_listener = $soap_mail_listener;
    }

    public function setContainer($container)
    {
        $this->container = $container;
    }

    private function getParameter($key)
    {
        return $this->container->getParameter($key);
    }
}
