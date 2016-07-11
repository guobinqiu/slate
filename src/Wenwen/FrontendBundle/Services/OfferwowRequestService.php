<?php

namespace Wenwen\FrontendBundle\Services;

use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;
use Jili\ApiBundle\Entity\OfferwowOrder;
use Jili\ApiBundle\Entity\User;
use Jili\ApiBundle\Component\OrderBase;

/**
 * 处理offerwow的数据回传
 * Usage:
 *       1. call validateParams
 *       2. call processEvent when the return of validateParams is 'success'
 */
class OfferwowRequestService
{
    private $logger;

    private $em;

    private $parameterService;

    private $offerwowParams;

    // Todo 20160707 统一到别处
    const TASK_TYPE_OFFERWOW = '5';
    const CATEGORY_TYPE_OFFERWOW = '17';

    const IMMEDIATE_0 = '0'; // 非即时返利活动,处于待审核状态
    const IMMEDIATE_1 = '1'; // 即时返利活动，需发放奖励给会员
    const IMMEDIATE_2 = '2'; // 非即时返利活动，审核通过，重新回传，发放奖励给会员
    const IMMEDIATE_3 = '3'; // 非即时返利活动，审核不通过，重新回传，不发放奖励

    public function __construct(LoggerInterface $logger,
                                EntityManager $em,
                                ParameterService $parameterService)
    {
        $this->logger = $logger;
        $this->em = $em;
        $this->parameterService = $parameterService;

        $this->offerwowParams = $this->parameterService->getParameter('offerwow_com');
    }

    /**
    *   a, 检查所有必须存在的参数是否存在，任何必须存在的参数，如果不存在的话，返回错误信息[offerwow-01]"出现空参数"
    *   b, 检查websiteid 是否匹配，如果不匹配的话，返回错误信息[offerwow-02]"网站id不存在" 
    *   c, 校验sign参数，校验不通过的话，返回错误信息[signature error]"签名验证出错"
    *   d, 检查memberid 是否为存在的user_id，如果不存在的话，返回错误信息[offerwow-03]"uid会员不存在"
    *   e, 检查该条订单号(eventid + immediate)是否已经存在与offerwow_order表中，
    *      如果已经存在，
    *         immediate为1 or 2的话，返回错误信息[offerwow-04]"已发放奖励的Eventid重复"
    *         immediate为0的话，返回错误信息[offerwow-05]
    *         immediate为3的话，返回错误信息[offerwow-06]
    *   f, 上面全都通过，结束validate
    *   @return array $result
    *                      status [success : 检查成功，可以继续处理]
    *                      status [failure : 检查失败，不能继续处理]
    *                      errno  [检查失败时的错误信息代号]
    */
    public function validateParams($memberid, $point, $eventid, $websiteid, $immediate, $sign){
        // a, 检查所有必须存在的参数是否存在，任何必须存在的参数，如果不存在的话，返回错误信息[offerwow-01]"出现空参数"
        if(is_null($memberid) || is_null($point) || is_null($eventid) || is_null($websiteid) || is_null($immediate) || is_null($sign) ){
            $result['status'] = 'failure';
            $result['errno'] = 'offerwow-01';
            $this->logger->debug(__METHOD__ . ' parameter is not enough. eventid=[' . $eventid . '] ' . $result['errno']);
            return $result;
        }

        // b, 检查websiteid 是否匹配，如果不匹配的话，返回错误信息[offerwow-02]"网站id不存在" 
        if($this->offerwowParams['websiteid'] !== $websiteid){
            $result['status'] = 'failure';
            $result['errno'] = 'offerwow-02';
            $this->logger->debug(__METHOD__ . ' websiteid is not correct. eventid=[' . $eventid . '] ' . $result['errno']);
            return $result;
        }

        // c, 校验sign参数，校验不通过的话，返回错误信息[signature error]"签名验证出错"
        $hash = array(
            $memberid,
            $point,
            $eventid,
            $websiteid,
            $immediate,
            $this->offerwowParams['key']
            );

        if( strtoupper(md5(implode($hash) )) !==  $sign ) {
            $result['status'] = 'failure';
            $result['errno'] = 'signature error';
            $this->logger->debug(__METHOD__ . ' sign is not correct. eventid=[' . $eventid . '] ' . $result['errno']);
            return $result;
        }

        // d, 检查memberid 是否为存在的user_id，如果不存在的话，返回错误信息[offerwow-03]"uid会员不存在"
        $user = $this->em->getRepository("JiliApiBundle:User")->findOneById($memberid);
        if(!$user){
            $result['status'] = 'failure';
            $result['errno'] = 'offerwow-03';
            $this->logger->debug(__METHOD__ . ' memberid is not exist. eventid=[' . $eventid . '] ' . $result['errno']);
            return $result;
        }

        // e, 检查该条订单号(eventid + immediate)是否已经存在与offerwow_order表中
        //      如果已经存在，
        //         immediate为1 or 2的话，返回错误信息[offerwow-04]"已发放奖励的Eventid重复"
        //         immediate为0的话，返回错误信息[offerwow-05]
        //         immediate为3的话，返回错误信息[offerwow-06]
        // 20160707 该表的eventid 似乎没有加index，记得加index
        $offerwowOrder = $this->em->getRepository("JiliApiBundle:OfferwowOrder")->findOneByEventid($eventid);
        if($offerwowOrder){
            if(self::IMMEDIATE_0 === $immediate) {
                // 无论已经存在的offerwow_order的状态是什么，这条数据回传都不用再处理了
                $result['status'] = 'failure';
                $result['errno'] = 'offerwow-05';
                $this->logger->warn(__METHOD__ . ' already processed request. eventid=[' . $eventid . '] ' . $result['errno']);
                return $result;
            } elseif(self::IMMEDIATE_1 === $immediate){
                // 无论已经存在的offerwow_order的状态是什么，这条数据回传都不用再处理了
                $result['status'] = 'failure';
                $result['errno'] = 'offerwow-04';
                $this->logger->warn(__METHOD__ . ' already processed request. eventid=[' . $eventid . '] ' . $result['errno']);
                return $result;
            } elseif(self::IMMEDIATE_2 === $immediate){
                // 原有的设计更改了初始状态，没法比较，只能先留用原有的方法，来判断是否complete
                $this->logger->debug(__METHOD__ . ' XXX. eventid=[' . $eventid . '] offerwow_order.status=['. $offerwowOrder->getStatus() .']');
                if(OrderBase::isCompleteStatus($offerwowOrder->getStatus())){
                    // 2 为终结状态，只允许从 0 -> 2
                    $result['status'] = 'failure';
                    $result['errno'] = 'offerwow-04';
                    $this->logger->warn(__METHOD__ . ' already processed request. eventid=[' . $eventid . '] ' . $result['errno']);
                    return $result;
                }
            } elseif(self::IMMEDIATE_3 === $immediate){
                // 原有的设计更改了初始状态，没法比较，只能先留用原有的方法，来判断是否complete
                if(OrderBase::isCompleteStatus($offerwowOrder->getStatus())){
                    // 3 为终结状态，只允许从 0 -> 3
                    $result['status'] = 'failure';
                    $result['errno'] = 'offerwow-06';
                    $this->logger->warn(__METHOD__ . ' already processed request. eventid=[' . $eventid . '] ' . $result['errno']);
                    return $result;
                }
            } else{
                $result['status'] = 'failure';
                $result['errno'] = 'unknown immediate';
                $this->logger->warn(__METHOD__ . ' unknown immediate. eventid=[' . $eventid . '] ' . $result['errno']);
                return $result;
            }
        }
        $result['status'] = 'success';
        return $result;
    }

    /**
    *   1, 更新或者新建 offerwow_order 记录原始订单信息
    *   2, 更新或者新建 task_history 记录任务信息
    *   3, $immediate 是 1 or 2 的时候， 新建point_history，更新user.point 
    */
    public function processEvent($userId, $point, $eventid, $immediate, $programname){
        $happenTime = date_create();
        $taskName = trim($programname);
        $status = self::convertStatus($immediate);
        if(!$taskName){
            // 万一 programname 没有设值的话，就用订单号代替任务名
            $taskName = $eventid;
        }

        // 更新或者新建 offerwow_order
        $offerwowOrder = $this->em->getRepository("JiliApiBundle:OfferwowOrder")->findOneByEventid($eventid);
        if(! $offerwowOrder) {
            // offerwow_order表里不存在这个eventid，记录下该条eventid
            $offerwowOrder = new OfferwowOrder();
            $offerwowOrder->setUserid($userId); 
            $offerwowOrder->setEventid($eventid); 
            $offerwowOrder->setStatus($status); 
            $offerwowOrder->setHappenedAt($happenTime);
            $offerwowOrder->setCreatedAt($happenTime);
            $offerwowOrder->setDeleteFlag(0);
        } else {
            $offerwowOrder->setStatus($status); 
            $offerwowOrder->setConfirmedAt($happenTime);
        }
        // 更新订单信息
        $this->em->persist($offerwowOrder);
        $this->em->flush();

        $taskRepository = $this->em->getRepository('JiliApiBundle:TaskHistory0'. ($userId % 10));
        // 20160707 offerwow 在 task_history中的 task_type = 5
        //          没有必要把这个写在paramter里面，统一在代码里就可以，暂时先这样写死
        $taskHistory = $taskRepository->findOneBy(array( 'orderId'=> $offerwowOrder->getId(),'taskType'=> self::TASK_TYPE_OFFERWOW) );
        if(! $taskHistory){
            $taskHistoryClass = 'Jili\ApiBundle\Entity\TaskHistory0'. ($userId % 10);
            $taskHistory = new $taskHistoryClass();
            $taskHistory->setUserid($userId);
            $taskHistory->setOrderId($offerwowOrder->getId());
            $taskHistory->setOcdCreatedDate($happenTime);
            $taskHistory->setCategoryType(self::CATEGORY_TYPE_OFFERWOW);
            $taskHistory->setTaskType(self::TASK_TYPE_OFFERWOW);
            $taskHistory->setTaskName($taskName);
            $taskHistory->setDate($happenTime);
            $taskHistory->setPoint($point);
            $taskHistory->setStatus($status);
        } else {
            $taskHistory->setStatus($status);
            $taskHistory->setDate($happenTime);
            $taskHistory->setPoint($point);
        }

        $dbConnection = $this->em->getConnection();
        $dbConnection->beginTransaction();
        try{
            // 20160707 更新task_history
            $this->em->persist($taskHistory);
            $this->logger->debug(__METHOD__ . ' XXX. eventid=[' . $eventid . '] offerwow_order.status=['. $status .'] ' . OrderBase::isCompleteStatus($status));
            // 20160707 给用户发放积分
            if(self::IMMEDIATE_1 === $immediate || self::IMMEDIATE_2 === $immediate){
                $pointHistoryClass = 'Jili\ApiBundle\Entity\PointHistory0'. ( $userId % 10);
                $pointHistory = new $pointHistoryClass();
                $pointHistory->setUserId($userId);
                $pointHistory->setPointChangeNum($point);
                $pointHistory->setReason(self::CATEGORY_TYPE_OFFERWOW);
                $user = $this->em->getRepository('JiliApiBundle:User')->find($userId);
                $user->setPoints(intval($user->getPoints()) + intval($point));
                $this->em->persist($pointHistory);
                $this->em->persist($user);
            }
            $this->em->flush();
            $dbConnection->commit();
        } catch (\Exception $e) {
            $dbConnection->rollback();
            $this->logger->error(__METHOD__ . 'eventid=[' . $eventid . '] user_id=[' . $userId . '] ' . $e->getMessage());
            return false;
        }
        return true;
    }


    /**
    *  无奈之举，暂时沿用原有的结构设计 offerwow_order里记录的状态沿用现在的变化
    *  注意，这其实只是一个private函数，供这个service用的，为了测试的时候准备测试数据方便，做成了public，偷懒了
    */
    public static function convertStatus($immediate){
        if(self::IMMEDIATE_0 === $immediate){
            return OrderBase::getPendingStatus();
        } elseif(self::IMMEDIATE_1 === $immediate){
            return OrderBase::getSuccessStatus();
        } elseif(self::IMMEDIATE_2 === $immediate){
            return OrderBase::getSuccessStatus();
        } elseif(self::IMMEDIATE_3 === $immediate){
            return OrderBase::getFailedStatus();
        }
    }
}