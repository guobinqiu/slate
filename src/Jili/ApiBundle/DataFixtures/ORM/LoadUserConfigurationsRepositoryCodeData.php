<?php
namespace Jili\ApiBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;


use Jili\ApiBundle\Entity\User;
use Jili\ApiBundle\Entity\UserConfigurations;

class LoadUserConfigurationsCodeData extends AbstractFixture {
    static public $ROWS;

    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct()
    {
        self::$ROWS = array();
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
