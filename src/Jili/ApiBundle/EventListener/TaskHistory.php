<?php
namespace Jili\ApiBundle\EventListener;

use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Doctrine\ORM\EntityManager;

use Jili\ApiBundle\Entity\TaskHistory00,
    Jili\ApiBundle\Entity\TaskHistory01,
    Jili\ApiBundle\Entity\TaskHistory02,
    Jili\ApiBundle\Entity\TaskHistory03,
    Jili\ApiBundle\Entity\TaskHistory04,
    Jili\ApiBundle\Entity\TaskHistory05,
    Jili\ApiBundle\Entity\TaskHistory06,
    Jili\ApiBundle\Entity\TaskHistory07,
    Jili\ApiBundle\Entity\TaskHistory08,
    Jili\ApiBundle\Entity\TaskHistory09;


/**
 * 
 **/
class TaskHistory 
{
    
    private $em;
    private $logger;

    public function __construct(LoggerInterface $logger, EntityManager $em/*, ParameterBagInterface $parameterBag*/ )
    {
        $this->logger = $logger;
        $this->em = $em;
    }

    /**
     *  dwprivate function updateTaskHistory($params=array()){
     *  @param: $params   'userid' => 1057622,
     *                     'orderId' => 2,
     *                     'taskType' => 1,
     *                     'reward_percent' => '',
     *                     'point' => 17,
     *                     'date' => 
     *                     DateTime::__set_state(array(
     *                        'date' => '2014-01-03 13:46:23',
     *                        'timezone_type' => 3,
     *                        'timezone' => 'Asia/Hong_Kong',
     *                     )),
     *                     'status' => 4,
     * @return null
     */
    public function update( array $params=array()){

        $em = $this->em;
        extract($params);

        $flag =  $userid % 10;
        $taskRepository = $em->getRepository('JiliApiBundle:TaskHistory0'. $flag); 

        //TODO: update more simplicity.
        $taskHistory= $taskRepository->findOneBy(array( 'orderId'=> $orderId,'taskType'=> $taskType) );

        if($taskHistory) {
            $po = $taskRepository->findOneById($taskHistory->getId() );
            $po->setDate($date);

            if(isset($point)) {
                $po->setPoint( $point );
            }
            if(isset($reward_percent)) {
                $po->setRewardPercent( $reward_percent );
            }

            $po->setStatus($status);
            $em->persist($po);
            $em->flush();
        } else {
            $po = null;
        }
        return $po;

    }

    public function init( array $params=array()){
        extract($params);
        $em = $this->em;
        $flag =  $userid % 10;
        $task_history = 'Jili\ApiBundle\Entity\TaskHistory0'. $flag;
        $po = new $task_history();
        $po->setUserid($userid);
        $po->setOrderId($orderId);
        $po->setOcdCreatedDate($date);
        $po->setCategoryType($categoryType);
        $po->setTaskType($taskType);
        $po->setTaskName(trim($task_name));
        $po->setDate($date);
        $po->setPoint( $point);
        $po->setStatus($status);

        if(isset($reward_percent)) {
            $po->setRewardPercent( $reward_percent );
        }

        $em->persist($po);
        $em->flush();
    }

    /**
     * @param: $params =  array (
     *     'user_id' => 1057622,
     *     'order_id' => 2,
     *   )
     * @return an TaskHistory{xx} instance.
     */
    public function selectPercent( array $params ){
        $em = $this->em;
        extract($params);
        $user_id = (int) $user_id;
        $flag =  $user_id % 10;

        $task_order = $em->getRepository('JiliApiBundle:TaskHistory0'. $flag)
            ->getTaskPercent($order_id);
        return $task_order[0];
    }

    
}


