<?php
namespace Jili\ApiBundle\Services\Session\User;

use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Doctrine\ORM\EntityManager;
/**
 * read the configs of login user into session
 **/
class Configs 
{

    private $session;
    private $em;
    private $logger;

    private $keys;

    public function __construct($keys)
    {
        $this->keys = $keys;
    }


    /**
     * checkin_op_method 0: manual , 1: auto
     */
    public function updateCheckinOpMethod()
    {
        $session = $this->session;
        $uid = $session->get('uid');
        if( ! $uid ) {
            return null;
        }
        $value =  $this->em->getRepository('JiliApiBundle:UserConfigurations')
            ->isAutoCheckin($uid);

        $session->set($this->keys['checkin_op_method'], (int) $value);
        return $value;
    }

    /**
     *
     */
    public function getCheckinOpMethod()
    {
        $session = $this->session;
        $key = $this->keys['checkin_op_method'];
        if( $session->has($key)) {
            return  $session->get($key);
        } 
        return $this->updateCheckinOpMethod();
    }


    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
        return $this;
    }

    public function setSession($session)
    {
        $this->session = $session;
        return $this;
    }
    public function setEntityManager(EntityManager $em)
    {
        $this->em = $em;
        return $this;
    }
}
