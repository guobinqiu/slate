<?php
namespace Jili\ApiBundle\Services\Points; 

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\Log\LoggerInterface;

class Manager
{
    private $em;

    /**
     * 更新point: user, point_history , task_history
     */
    public function updatePoints($userId, $point,  $adCategoryId, $taskTypeId, $taskName)
    {

        $em = $this->em;//getDoctrine()->getManager();
        $user = $em->getRepository('JiliApiBundle:User')->find($userId);

        if( ! isset($user ) ) {
            $this->logger->info('UpdatePoints , user not exists user.id='.var_export($userId,true));
            return ;
        }

        $updateTime = date_create(date('Y-m-d H:i:s'));

        // transaction start
        $dbh = $em->getConnection();
        $dbh->beginTransaction();

        try {

            //更新user表总分数
            $old_point = $user->getPoints();
            $user->setPoints(intval($old_point + $point));

            // Create new object of point_history0x
            $classPointHistory = 'Jili\ApiBundle\Entity\PointHistory0'. ( $userId % 10);
            $pointHistory = new $classPointHistory();
            $pointHistory->setUserId($userId);
            $pointHistory->setPointChangeNum($point);
            $pointHistory->setReason($adCategoryId);

            // Create new object of task_history0x
            $classTaskHistory = 'Jili\ApiBundle\Entity\TaskHistory0'. ( $userId % 10);
            $taskHistory = new $classTaskHistory();
            $taskHistory->setUserid($userId);
            $taskHistory->setOrderId(0);
            $taskHistory->setOcdCreatedDate($updateTime);
            $taskHistory->setCategoryType($adCategoryId);
            $taskHistory->setTaskType($taskTypeId);
            $taskHistory->setTaskName($taskName);
            $taskHistory->setDate($updateTime);
            $taskHistory->setPoint($point);
            $taskHistory->setStatus(1);

            $em->persist($user);
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
