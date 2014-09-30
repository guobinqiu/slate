<?php
namespace Jili\ApiBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;


use Jili\ApiBundle\Entity\User;
use Jili\ApiBundle\Entity\UserConfigurations;
      
class LoadUserConfigurationsRepositoryCodeData extends AbstractFixture  implements ContainerAwareInterface, FixtureInterface
{
    static public $ROWS;

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
    public function  load(ObjectManager $manager) 
    {

        $user = new User();
        $user->setNick('alice32');
        $user->setEmail('alice.nima@gmail.com');
        $user->setPoints($this->container->getParameter('init'));
        $user->setIsInfoSet($this->container->getParameter('init'));
        $user->setRewardMultiple($this->container->getParameter('init_one'));

        $user->setPwd('aaaaaa');
        $manager->persist($user);
        $manager->flush();

        // user1
        // user2
        // user3

    }


}
