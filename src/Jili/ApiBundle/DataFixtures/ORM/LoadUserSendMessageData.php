<?php
namespace Jili\ApiBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Jili\ApiBundle\Entity\User;
use Jili\ApiBundle\Utility\SequenseEntityClassFactory;
use Jili\ApiBundle\Entity\SendMessage00;
use Jili\ApiBundle\Entity\SendMessage01;
use Jili\ApiBundle\Entity\SendMessage02;
use Jili\ApiBundle\Entity\SendMessage03;
use Jili\ApiBundle\Entity\SendMessage04;
use Jili\ApiBundle\Entity\SendMessage05;
use Jili\ApiBundle\Entity\SendMessage06;
use Jili\ApiBundle\Entity\SendMessage07;
use Jili\ApiBundle\Entity\SendMessage08;
use Jili\ApiBundle\Entity\SendMessage09;

class LoadUserSendMessageData extends AbstractFixture implements ContainerAwareInterface, FixtureInterface, OrderedFixtureInterface {

    public static $USER;
    public static $SEND_MESSAGE;

    /**
    * @var ContainerInterface
    */
    private $container;

    public function __construct() {
        self :: $SEND_MESSAGE = array ();
        self :: $USER = array ();
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
    public function getOrder() {
        return 1; // the order in which fixtures will be loaded
    }

    /**
    * {@inheritDoc}
    */
    public function load(ObjectManager $manager) {
        //load data for testing .
        $user = new User();
        $user->setNick('aa');
        $user->setEmail('sendmessage@voyagegroup.com.cn');
        $user->setPoints(5000);
        $user->setIsInfoSet(0);
        $user->setRewardMultiple(1);
        $user->setPwd('111111');
        $manager->persist($user);
        $manager->flush();

        self :: $USER = $user;

        $sm = SequenseEntityClassFactory :: createInstance('SendMessage', $user->getId());
        $sm->setSendFrom(0);
        $sm->setSendTo($user->getId());
        $sm->setTitle("title");
        $sm->setContent("content");
        $sm->setReadFlag(0);
        $sm->setDeleteFlag(0);
        $manager->persist($sm);
        $manager->flush();

        self :: $SEND_MESSAGE = $sm;
    }
}