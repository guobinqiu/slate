<?php
namespace Jili\ApiBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Jili\ApiBundle\Entity\TaskHistory04;

class LoadAdminSelectTaskPercentCodeData extends AbstractFixture {

    public static $TASK_HISTORY;

    public function __construct() {
        self :: $TASK_HISTORY = array ();
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager) {
        $userid = 1057704;
        $orderId = 1;

        $r = new TaskHistory04();
        $r->setUserId($userid);
        $r->setOrderId($orderId);
        $r->setTaskType(1);
        $r->setCategoryType(2);
        $r->setTaskName('亚马逊');
        $r->setRewardPercent(20);
        $r->setPoint(0);
        $r->setStatus(1);

        $manager->persist($r);
        $manager->flush();

        self :: $TASK_HISTORY = $r;
    }
}