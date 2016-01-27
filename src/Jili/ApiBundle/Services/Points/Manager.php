<?php
namespace Jili\ApiBundle\Services\Points; 

class Manager
{
    private $point_history;
    private $task_history;
    private $em;

    /**
     * 更新point: user, point_history , task_history
     */
    public function updatePoint($userId,$point, $ad_category_id, $task_name)
    {
        $em = $this->em;//getDoctrine()->getManager();
        $dbh = $em->getConnection();
        // transaction start
        $dbh->beginTransaction();

        //更新user表总分数
        $user = $em->getRepository('JiliApiBundle:User')->find($userId);
        if( ! isset($userId)) {
            return ;
        }

        try {

            $oldPoint = $user->getPoints();
            $user->setPoints(intval($oldPoint+$point));

            //更新point_history表分数
            $params = array (
                'userid' => $userId,
                'point' => $point,
                'type' => $ad_category_id,//9:完善资料
            );
            $this->point_history->get($params);
            //更新task_history表分数
            $params = array (
                'userid' => $userId,
                'orderId' => 0,
                'taskType' => 4,
                'categoryType' => $ad_category_id,//9:完善资料
                'task_name' => $task_name 
                'point' => $point,
                'date' => date_create(date('Y-m-d H:i:s')),
                'status' => 1
            );
            $this->task_history->init($params);
            $em->flush();

            $dbh->commit();
        } catch(\Exception $e) {
            $dbh->rollback();
        }

    }

    public function setTaskHistory( $th) 
    {
        $this->task_history = $th;
    }
    public function setPointHistory( $ph) 
    {
        $this->point_history = $ph;
    }
    public function  setEntityManager($em)
    {
        $this->em = $em;
    }
}
