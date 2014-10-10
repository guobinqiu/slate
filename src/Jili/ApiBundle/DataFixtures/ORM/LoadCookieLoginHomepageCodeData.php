<?php
namespace Jili\ApiBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Jili\ApiBundle\Entity\User;


class LoadCookieLoginHomepageCodeData  extends AbstractFixture implements ContainerAwareInterface, FixtureInterface
{
    public static $ROWS;
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct()
    {
        self::$ROWS = array();
    }
    /**
    * {@inheritDoc}
    */
    public function setContainer(ContainerInterface $container = null) {
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {

        $query = array('email'=> 'chiangtor@gmail.com');
        //load data for testing .
        $user = new User();
        $user->setNick('bb');
        $user->setEmail('chiangtor@gmail.com');
        $user->setPoints(100);
        $user->setIsInfoSet(0);
        $user->setRewardMultiple(1);
        $user->setPwd('aaaaaa');
        $secret =  $this->container->getParameter('secret');
        $token = $this->buildToken(array('email'=> 'chiangtor@gmail.com', 'pwd'=> 'aaaaaa') , $secret );
        $user->setToken( $token);

        $date = new \DateTime();
        $date->sub(new \DateInterval('P6D'));
        $user->setTokenCreatedAt($date);

        $manager->persist($user);
        $manager->flush();

        self :: $ROWS[] = $user;

    }

    private function buildToken($user , $secret)
    {
        $token = implode('|',$user) .$secret;//.$this->getParameter('secret') ;
        $token = hash('sha256', $token);
        $token = substr( $token, 0 ,32);
        return $token;
    }
}
