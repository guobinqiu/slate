<?php
namespace Jili\ApiBundle\Services\Points; 

use Jili\ApiBundle\EventListener\TaskHistory;
use Jili\ApiBundle\EventListener\PointHistory;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\Log\LoggerInterface;

class Manager
{
    private $em;

    /**
     * 更新point: user, point_history , task_history
     */
    public function updatePoints($user_id, $point,  $ad_category_id, $task_type_id, $task_name)
    {

        $em = $this->em;//getDoctrine()->getManager();
        $user = $em->getRepository('JiliApiBundle:User')->find($user_id);

        if( ! isset($user ) ) {
            $this->logger->info('UpdatePoints , user not exists user.id='.var_export($user_id,true));
            return ;
        }

        $update_time = date_create(date('Y-m-d H:i:s'));

        // transaction start
        $dbh = $em->getConnection();
        $dbh->beginTransaction();

        try {

            //更新user表总分数
            $old_point = $user->getPoints();
            $user->setPoints(intval($old_point + $point));

            // Create new object of point_history0x
            $classPointHistory = 'Jili\ApiBundle\Entity\PointHistory0'. ( $user_id % 10);
            $pointHistory = new $classPointHistory();
            $pointHistory->setUserId($user_id);
            $pointHistory->setPointChangeNum($point);
            $pointHistory->setReason($ad_category_id);

            // Create new object of task_history0x
            $classTaskHistory = 'Jili\ApiBundle\Entity\TaskHistory0'. ( $user_id % 10);
            $taskHistory = new $classTaskHistory();
            $taskHistory->setUserid($user_id);
            $taskHistory->setOrderId(0);
            $taskHistory->setOcdCreatedDate($update_time);
            $taskHistory->setCategoryType($ad_category_id);
            $taskHistory->setTaskType($task_type_id);
            $taskHistory->setTaskName($task_name);
            $taskHistory->setDate($update_time);
            $taskHistory->setPoint($point);
            $taskHistory->setStatus(1);

            $em->persist($pointHistory);
            $em->persist($taskHistory);
            $em->flush();
            $dbh->commit();
        } catch(\Exception $e) {
            $dbh->rollback();
            $this->logger->crit( $e->getMessage() );
        }

    }

    public function  setEntityManager(EntityManager $em)
    {
        $this->em = $em;
        return $this;
    }
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
        return $this;
    }

}
