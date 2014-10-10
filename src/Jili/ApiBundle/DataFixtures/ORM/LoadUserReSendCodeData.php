<?php
namespace Jili\ApiBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Jili\ApiBundle\Entity\User;

class LoadUserReSendCodeData extends AbstractFixture implements ContainerAwareInterface, FixtureInterface
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
        $user = new User();
        $user->setNick('zmm');
        $user->setEmail('zhangmm@voyagegroup.com.cn');
        $user->setPoints($this->container->getParameter('init'));
        $user->setIsInfoSet($this->container->getParameter('init'));
        $user->setRewardMultiple($this->container->getParameter('init_one'));
        $user->setPwd('123qwe');
        $manager->persist($user);
        $manager->flush();
        self::$ROWS[] = $user;
    }

}

