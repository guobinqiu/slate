<?php
namespace Jili\ApiBundle\Services\AdwAdmin;

use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Doctrine\ORM\EntityManager;
use Jili\ApiBundle\Component\OrderBase;

class DataConfirmedProcessor 
{
    private $em;
    private $logger;

    private $order_status;

    public function __construct() 
    {
        $this->order_status = OrderBase::getStatusList(); 
    }


    /**
     * noCertified 
     * 
     *  没有通过认证
     *
     * @param mixed $userId 
     * @param mixed $adid 
     * @param mixed $ocd 
     * @access public
     * @return void
     */
    public function noCertified($userId,$adid,$ocd)
    {
        $order_status = $this->order_status;
        $em = $this->em;
        $logger = $this->logger ;

        $advertiserment = $em->getRepository('JiliApiBundle:Advertiserment')->find($adid);

        $adworder = $em->getRepository('JiliApiBundle:AdwOrder')
            ->getOrderInfo($userId,$adid,$ocd);

        if(empty($adworder)){
            return false;
        }

        $adworder = $em->getRepository('JiliApiBundle:AdwOrder')
            ->find($adworder[0]['id']);

        // transaction: 
        $connection = $em->getConnection();
        $connection->beginTransaction();
        try{
            $adworder->setConfirmTime(date_create(date('Y-m-d H:i:s')))
                ->setOrderStatus($order_status['COMPLETED_FAILED']);

            $em->persist($adworder);
            $em->flush();

            $parms = array(
                'userId' => $userId,
                'orderId' => $adworder->getId(),
                'taskType' => 1, // adw cps
                'categoryType'=> $adworder->getIncentiveType(),
                'rewardPercent' => 0, //no use for uncertified 
                'point' => $adworder->getIncentive(),
                'status' => $order_status['COMPLETED_FAILED'],
                'statusPrevious' => $order_status['PENDING'],
                'date'=> new \DateTime()
            );

            $return = $em->getRepository('JiliApiBundle:TaskHistory0'. ($userId % 10))
                ->update($parms);

            if( $return == 0) {
               throw new \Exception('task history no updated with return '. var_export($return, true)); 
            }

            $connection->commit();
            return true;
        } catch ( \Exception $e) {
            $logger->error( $e->getMessage() );
            $connection->rollback();
            return false;
        }
    }

    /**
     * hasCertified 
     * 已经认证
     * 
     * @param mixed $userId 
     * @param mixed $adid 
     * @param mixed $ocd 
     * @param mixed $comm 
     * @access public
     * @return void
     */
    public function hasCertified($userId,$adid,$ocd,$comm)
    {
        $order_status = $this->order_status;
        $em = $this->em;
        $advertiserment = $em->getRepository('JiliApiBundle:Advertiserment')->find($adid);

        if($advertiserment->getIncentiveType()!=2){
            return false;
        }

        $adworder = $em->getRepository('JiliApiBundle:AdwOrder')
            ->getOrderInfo($userId,$adid, $ocd);


        if(empty($adworder)){
            return false;
        }

        $adworder = $em->getRepository('JiliApiBundle:AdwOrder')->find($adworder[0]['id']);


        if($adworder->getIncentiveType()!=2){
            return false;
        }

        $task_order = $em->getRepository('JiliApiBundle:TaskHistory0'.( $userId % 10))
            ->getTaskPercent($adworder->getId());

        $taskPercent =  $task_order[0];

        $adworder->setConfirmTime(date_create(date('Y-m-d H:i:s')))
            ->setOrderStatus($order_status['COMPLETED_SUCCEEDED']);

        $em->persist($adworder);
        $em->flush();

        $parms = array(
            'userid' => $userId,
            'orderId' => $adworder->getId(),
            'taskType' => 1,
            'categoryType'=> $adworder->getIncentiveType(),
            'point' => intval($comm*$taskPercent['rewardPercent']),
            'date' => date('Y-m-d H:i:s'),
            'status' => $order_status['COMPLETED_SUCCEEDED'] ,
            'statusPrevious' => $order_status['PENDING'],
        );


        $return = $em->getRepository('JiliApiBundle:TaskHistory0'. ($userId % 10))
            ->update($parms);

        if(!$return){
            return false;
        }

        $limitAd = $em->getRepository('JiliApiBundle:LimitAd')->findByAdId($adid);
        $limitrs = new LimitAdResult();
        $limitrs->setAccessHistoryId($adworder->getId());
        $limitrs->setUserId($userId);
        $limitrs->setLimitAdId($limitAd[0]->getId());
        $limitrs->setResultIncentive($adworder->getIncentive());

        $em->persist($limitrs);
        $em->flush();

        $em->getRepository('JiliApiBundle:PointHistory'. ($userId % 10))
            ->get(array('userid'=>$userId,
                'point'=> $adworder->getIncentive(),
                'type' => $adworder->getIncentiveType()));

        $user = $em->getRepository('JiliApiBundle:User')
            ->updatePointById(array('id'=> $userId,
                'points'=> $adworder->getIncentive()));
        

        return true;
    }


    public function setEntityManager(EntityManager $em) {
        $this->em = $em;
        return $this;
    }

    public function setLogger(LoggerInterface $logger) {
        $this->logger = $logger;
        return $this;
    }
}
