<?php
namespace Jili\ApiBundle\Services\Points; 

use Jili\ApiBundle\EventListener\TaskHistory;
use Jili\ApiBundle\EventListener\PointHistory;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\Log\LoggerInterface;

class Manager
{
    private $point_history;
    private $task_history;
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

        // transaction start
        $dbh = $em->getConnection();
        $dbh->beginTransaction();

        try {

            //更新user表总分数
            $old_point = $user->getPoints();
            $user->setPoints(intval($old_point + $point));

            //更新point_history表分数
            $params = array (
                'userid' => $user_id,
                'point' => $point,
                'type' => $ad_category_id,//ad_category.id
            );
            $this->point_history->get($params);
            //更新task_history表分数
            $params = array (
                'userid' => $user_id,
                'orderId' => 0,
                'taskType' => $task_type_id, // refer to task_history00 entity 
                'categoryType' => $ad_category_id,
                'task_name' => $task_name ,
                'point' => $point,
                'date' => date_create(date('Y-m-d H:i:s')),
                'status' => 1
            );

            $this->task_history->init($params);

            $em->flush();
            $dbh->commit();
        } catch(\Exception $e) {
            $dbh->rollback();
            $this->logger->crit( $e->getMessage() );
        }

    }

    public function setTaskHistory( TaskHistory $th) 
    {
        $this->task_history = $th;
        return $this;
    }
    public function setPointHistory(PointHistory  $ph) 
    {
        $this->point_history = $ph;
        return $this;
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
