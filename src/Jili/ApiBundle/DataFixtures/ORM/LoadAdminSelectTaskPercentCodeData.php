<?php
namespace Jili\ApiBundle\DataFixtures\ORM;


use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Jili\ApiBundle\Entity\TaskHistory04;

class LoadAdminSelectTaskPercentCodeData extends AbstractFixture
{
    public static $ROWS;

    public function __construct() {
        self :: $ROWS= array ();
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager) {
        $userid = 1057704;
        $orderId = 1;
/*
      id: 225386
        order_id: 606483
         user_id: 1234584
       task_type: 1
   category_type: 2
       task_name: 亚马逊
  reward_percent: NULL
           point: 0
ocd_created_date: 2014-09-03 01:34:53
            date: 2014-09-03 01:34:53
          status: 1
 */
        $r = new TaskHistory04();
        $r->setUserId($userid);
        $r->setOrderId($orderId);
        $r->setTaskType(1);
        $r->setCategoryType(2);
        $r->setTaskName('亚马逊');
        $r->setPoint(0);
        $r->setStatus(1);

        $manager->persist($r);
        $manager->flush();
        self::$ROWS[] = $r;


    }
}

