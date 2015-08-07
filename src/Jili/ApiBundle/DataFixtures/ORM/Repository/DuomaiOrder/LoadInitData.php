<?php
namespace Jili\ApiBundle\DataFixtures\ORM\Repository\DuomaiOrder;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Jili\ApiBundle\Entity\User;
use Jili\ApiBundle\Entity\DuomaiOrder;
use Jili\ApiBundle\Entity\TaskHistory05;

class LoadInitData extends AbstractFixture implements  FixtureInterface {



    /**
    * {@inheritDoc}
    */
    public function load(ObjectManager $manager) {
        //load data for testing .

        $r1 = $manager->getConnection()->query("INSERT INTO `user` (`id`, `email`, `pwd`, `is_email_confirmed`, `is_from_wenwen`, `wenwen_user`, `token`, `nick`, `sex`, `birthday`, `tel`, `is_tel_confirmed`, `province`, `city`, `education`, `profession`, `income`, `hobby`, `personalDes`, `identity_num`, `reward_multiple`, `register_date`, `last_login_date`, `last_login_ip`, `points`, `delete_flag`, `is_info_set`, `icon_path`, `uniqkey`, `token_created_at`) VALUES (105,'chiangtor@gmail.com','7160b513da27b8e99bb8f05399ebf8824fd9186a',NULL,NULL,NULL,'','chiang32',1,'1981-11','',NULL,25,322,NULL,NULL,100,'1,2,3,4,5,6,7,8,9,10,11,12',NULL,NULL,1,'2013-06-08 14:25:24','2015-04-27 10:25:57','127.0.0.1',98236,NULL,1,'uploads/user/5/1392030971_6586.jpeg',NULL,'2014-11-05 16:50:52')");
        $r1->closeCursor();

        // 已经初始化的订单数据(status=0过后)
        $r2 = $manager->getConnection()->query("INSERT INTO duomai_order (id, user_id, ocd, ads_id, ads_name, site_id, link_id, order_sn, order_time, orders_price, comm, status, deactivated_at, confirmed_at, balanced_at, created_at) VALUES (1,'105', '71440050', '61', '京东商城CPS推广', '152244', '0', '9152050154', '2015-04-27 10:28:59', '799.91', '0', 1, '1970-01-01 08:00:00', '1970-01-01 08:00:00', '1970-01-01 08:00:00', '2015-04-27 11:58:32')");
        $r2->closeCursor();

        // 已经初始化的任务历史数据(status=0之后)
        $r3 = $manager->getConnection()->query("INSERT INTO task_history05 (id, order_id, user_id, task_type, category_type, task_name, reward_percent, point, ocd_created_date, date, status) VALUES (1 , 1, '105', 8, 23, '购物返利', NULL, 0, '2015-04-27 11:58:32', '2015-04-27 11:58:32', 1)");
        $r3->closeCursor();
        # task_history 

        # duomai_order;
    }
}
