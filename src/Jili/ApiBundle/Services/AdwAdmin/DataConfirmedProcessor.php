<?php
namespace Jili\ApiBundle\Services\AdwAdmin;

use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Doctrine\ORM\EntityManager;

use Jili\ApiBundle\Component\OrderBase,
    Jili\ApiBundle\Entity\TaskHistory00,
    Jili\ApiBundle\Entity\AdCategory,
    Jili\ApiBundle\Entity\LimitAdResult;

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
    public function noCertified($userId,$adid,$ocd,$cps_advertisement = false)
    {
        $adid = (int) $adid;
        $userId = (int) $userId;
        $ocd = trim($ocd);

        if(  $userId <= 0 || strlen($ocd) === 0 ) {
            return false;
        }

        $order_status = $this->order_status;
        $em = $this->em;
        $logger = $this->logger ;

        // advertisement must be adw cps
        if(! $cps_advertisement ){
            $advertiserment = $em->getRepository('JiliApiBundle:Advertiserment')->find($adid);
            if(! $advertiserment || $advertiserment->getIncentiveType()!=2){
                return false;
            }
        }
        $status_ = '';
        $adworder = $em->getRepository('JiliApiBundle:AdwOrder')
            ->getOrderInfo($userId, $adid, $ocd, $status_ , $cps_advertisement);
        if(empty($adworder)){
            return false;
        }

        // transaction: 
        $connection = $em->getConnection();
        $connection->beginTransaction();
        try{
            $adworder = $em->getRepository('JiliApiBundle:AdwOrder')
                ->find($adworder[0]['id']);

            $adworder->setConfirmTime(date_create(date('Y-m-d H:i:s')))
                ->setOrderStatus($order_status['COMPLETED_FAILED']);

            $em->persist($adworder);
            $em->flush();

            $parms = array(
                'userId' => $userId,
                'orderId' => $adworder->getId(),
                'taskType' => TaskHistory00::TASK_TYPE_ADW, // adw 
                'categoryType'=> AdCategory::ID_ADW_CPS, 
                'reward_percent' => 0, //no use for uncertified 
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
        } catch ( \Exception $e) {
            $connection->rollback();
            $logger->error( '    =>' .$e->getMessage() );
            return false;
        }

        return true;
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
    public function hasCertified($userId,$adid,$ocd,$comm,$cps_advertisement = false)
    {
        $adid = (int) $adid;
        $userId = (int) $userId;
        $ocd = trim($ocd);

        $logger = $this->logger;
        if(  $userId <= 0 || strlen($ocd) === 0) {
            return false;
        }

        $order_status = $this->order_status;
        $em = $this->em;
        if(! $cps_advertisement ){
            $advertiserment = $em->getRepository('JiliApiBundle:Advertiserment')->find($adid);

            if(! $advertiserment || $advertiserment->getIncentiveType()!=2){
                return false;
            }
        }

        $adworder = $em->getRepository('JiliApiBundle:AdwOrder')
            ->getOrderInfo($userId, $adid, $ocd, '', $cps_advertisement);

        if(empty($adworder)){
            $logger->info('    =>No adw_order');
            return false;
        }

        $adworder = $em->getRepository('JiliApiBundle:AdwOrder')->find($adworder[0]['id']);

        if($adworder->getIncentiveType()!=2){
            return false;
        }


        $task_order = $em->getRepository('JiliApiBundle:TaskHistory0'.( $userId % 10))
            ->getTaskPercent($adworder->getId());

        $taskPercent =  $task_order[0];
        // use the comm in csv exclude the exists.
        $point = intval($comm * $taskPercent['rewardPercent']);

        $task_params = array(
            'userId' => $userId,
            'orderId' => $adworder->getId(),
            'taskType' => TaskHistory00::TASK_TYPE_ADW, // adw 
            'categoryType'=> AdCategory::ID_ADW_CPS, 
            'point' => $point,
            'date' => date('Y-m-d H:i:s'),
            'status' => $order_status['COMPLETED_SUCCEEDED'] ,
            'statusPrevious' => $order_status['PENDING'],
        );

        try {
            $em->getConnection()->beginTransaction();
            $adworder->setConfirmTime(date_create(date('Y-m-d H:i:s')))
                ->setIncentive( $point) 
                ->setOrderStatus($order_status['COMPLETED_SUCCEEDED']);

            $em->persist($adworder);

            $return = $em->getRepository('JiliApiBundle:TaskHistory0'. ($userId % 10))
                ->update($task_params);

            if(!$return){
                throw new Exception('Update task history failed');
            }

            // AdCategory::ID_ADW_CPS : 2, $adworder->getIncentiveType(cps): 2 
            $em->getRepository('JiliApiBundle:PointHistory0'. ($userId % 10))
                ->get(array(
                    'userid'=>$userId,
                    'point'=> $point,
                    'type' => AdCategory::ID_ADW_CPS)); 

            $user = $em->getRepository('JiliApiBundle:User')
                ->updatePointById(array(
                    'id'=> $userId,
                    'points'=> $point));

            $em->flush();
            $em->getConnection()->commit();
            $em->getConnection()->rollback();
            $logger->info('    => ok' );
        } catch (\Exception $e) {
            $em->getConnection()->rollback();
            $logger->info('    => [exception]'. $e->getMessage());
            return false;
        }
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
