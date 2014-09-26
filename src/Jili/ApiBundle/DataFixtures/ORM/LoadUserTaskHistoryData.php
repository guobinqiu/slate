<?php
namespace Jili\ApiBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Jili\ApiBundle\Entity\User;
use Jili\ApiBundle\Entity\TaskHistory00;
use Jili\ApiBundle\Entity\TaskHistory01;
use Jili\ApiBundle\Entity\TaskHistory02;
use Jili\ApiBundle\Entity\TaskHistory03;
use Jili\ApiBundle\Entity\TaskHistory04;
use Jili\ApiBundle\Entity\TaskHistory05;
use Jili\ApiBundle\Entity\TaskHistory06;
use Jili\ApiBundle\Entity\TaskHistory07;
use Jili\ApiBundle\Entity\TaskHistory08;
use Jili\ApiBundle\Entity\TaskHistory09;
use Jili\ApiBundle\Utility\SequenseEntityClassFactory;

class LoadUserTaskHistoryData extends AbstractFixture implements ContainerAwareInterface, FixtureInterface, OrderedFixtureInterface {

    public static $TASK_HISTORY;

    public function __construct() {
        self :: $TASK_HISTORY = array ();
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
        $user->setNick('bb');
        $user->setEmail('task@voyagegroup.com.cn');
        $user->setPoints(100);
        $user->setIsInfoSet(0);
        $user->setRewardMultiple(1);
        $user->setPwd('111111');
        $manager->persist($user);
        $manager->flush();

        $po = SequenseEntityClassFactory :: createInstance('TaskHistory', $user->getId());
        $po->setOrderId(1);
        $po->setUserId($user->getId());
        $po->setTaskType(3);
        $po->setCategoryType(1);
        $po->setTaskName('广告体验');
        $po->setRewardPercent(0);
        $po->setPoint(500);
        $po->setDate(date_create());
        $po->setOcdCreatedDate(date_create());
        $po->setStatus(1);
        $manager->persist($po);
        $manager->flush();

        self :: $TASK_HISTORY = $po;
    }
}
