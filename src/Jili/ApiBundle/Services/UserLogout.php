<?php
namespace Jili\ApiBundle\Services;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ParameterBagInterface;
use Jili\ApiBundle\Entity\User;

/**
 *
 **/
class UserLogout
{
    private $em;
    private $container;

    public function __construct()
    {
    }

    /**
     * @param  $request
     */
    public function logout(Request $request)
    {
        $session = $request->getSession();

        if ($session->has('uid')) {
            $uid = $session->get('uid');
            $this->em->getRepository('JiliApiBundle:User')->cleanToken($uid);
        }

        $session->remove('uid');
        $session->remove('nick');
        $session->save();

        if ($session->has('referer')) {
            $referer_url = $session->get('referer');
            $session->remove('referer');
        }
    }

    public function setEntityManager(EntityManager $em)
    {
        $this->em = $em;
    }

    public function setContainer($c)
    {
        $this->container = $c;
    }
}
