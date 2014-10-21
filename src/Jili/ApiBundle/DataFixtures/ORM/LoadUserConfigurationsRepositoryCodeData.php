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
    static public $USER;
    static public $CONFIGS;

    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct()
    {
        self::$USER= array();
        self::$CONFIGS= array();
    }

    /**
    * {@inheritDoc}
    */
    public function setContainer(ContainerInterface $container = null) {
        $this->container = $container;
    }


    public function  load(ObjectManager $manager) 
    {
        // chiang32 without config
        $user = new User();
        $user->setNick('chiang32');
        $user->setEmail('chiangtor@gmail.com');
        $user->setPoints($this->container->getParameter('init'));
        $user->setIsInfoSet($this->container->getParameter('init'));
        $user->setRewardMultiple($this->container->getParameter('init_one'));
        $user->setPwd('123qwe');
        $manager->persist($user);
        $manager->flush();
        self::$USER[0] = $user;

        // alice32, flag = 1
        $user1 = new User();
        $user1->setNick('alice32');
        $user1->setEmail('alice.nima@gmail.com');
        $user1->setPoints($this->container->getParameter('init'));
        $user1->setIsInfoSet($this->container->getParameter('init'));
        $user1->setRewardMultiple($this->container->getParameter('init_one'));
        $user1->setPwd('123qwe');
        $manager->persist($user1);
        $manager->flush();
        self::$USER[1] = $user1;

        $userConfig1 = new UserConfigurations();
        $userConfig1->setUserId($user1->getId());
        $userConfig1->setFlagName('auto_checkin');
        $userConfig1->setFlagData(1);
        $manager->persist($userConfig1);
        $manager->flush();
        self::$CONFIGS [0] = $userConfig1;

        // bob32, flag = 0
        $user2 = new User();
        $user2->setNick('bob32');
        $user2->setEmail('bob.inch@gmail.com');
        $user2->setPoints($this->container->getParameter('init'));
        $user2->setIsInfoSet($this->container->getParameter('init'));
        $user2->setRewardMultiple($this->container->getParameter('init_one'));
        $user2->setPwd('123qwe');
        $manager->persist($user2);
        $manager->flush();
        self::$USER[2] = $user2;

        $userConfig2 = new UserConfigurations();
        $userConfig2->setUserId($user2->getId());
        $userConfig2->setFlagName('auto_checkin');
        $userConfig2->setFlagData(0);
        $manager->persist($userConfig2);
        $manager->flush();
        self::$CONFIGS [1] = $userConfig2;

        $userConfig2 = new UserConfigurations();
        $userConfig2->setUserId($user2->getId());
        $userConfig2->setFlagName('other_config');
        $userConfig2->setFlagData(1);
        $manager->persist($userConfig2);
        $manager->flush();
        self::$CONFIGS [2] = $userConfig2;
    }
}
