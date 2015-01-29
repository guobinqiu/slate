<?php

namespace Jili\BackendBundle\Services\GameEggsBreaker;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Jili\BackendBundle\Utility\PointsStrategy;
use Jili\BackendBundle\Utility\PointsPool;
use Jili\BackendBundle\Utility\TaobaoOrderToEggs;
use Jili\FrontendBundle\Entity\GameEggsBreakerTaobaoOrder;
use Jili\BackendBundle\Utility\JsonCacheFileHandler;
use Jili\FrontendBundle\Entity\GameEggsBreakerEggsInfo;
use Jili\ApiBundle\Entity\AdCategory;

// write ranking to cache file
// read ranking 
class GameEggsBreaker 
{
    /**
     * @var array
     */
    protected $configs ;
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;
    /**
     * @var \Symfony\Component\HttpKernel\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @param array $configs = array(
     *
     * )
     */
    function __construct( $configs)
    {
        $this->configs = $configs;
    }

    /**
     * @param string $rules
     * @param string $points_type common/consolation
     */
    public function publishPointsStrategy($rules, $points_type)
    {
        if(! isset( $this->configs[$points_type] ) )
        {
            throw new \Exception( 'points strategy path for ' .var_export($points_type, true). ' is not configured in '. var_export( $this->configs, true) );
        }
        $rules = str_replace(array("\r\n", "\r"), "\n", $rules);
        $rules_array  = explode("\n", $rules);
        $points_strategy = array();
        foreach( $rules_array  as $rule ) {
            list($frequnecy, $pts ) = explode(':' , $rule);
            $points_strategy[] = array( intval($frequnecy), intval($pts));
        }
        $file = $this->configs[$points_type]['points_strategy'];
//        if (count($points_strategy) === 0 ) {
//            throw new \Exception('No points strategy quried');
//        }
        $pointsStrategy = new PointsStrategy($file);
        return  $pointsStrategy->publish($points_strategy);
    }

    public function cleanPointsPool( $points_type)
    {
        if(! isset( $this->configs[$points_type] ) )
        {
            throw new \Exception( 'points strategy path for ' .var_export($points_type, true). ' is not configured in '. var_export( $this->configs, true) );
        }

        $points_file = $this->configs[$points_type]['points_pool'];
        $strategy_file = $this->configs[$points_type]['points_pool'];

        $pointsPool = new PointsPool( $this->configs[$points_type]['points_pool']
            ,$this->configs[$points_type]['points_strategy'] );
        $pointsPool->cleanPointsPool( );
    }
    /**
     * @param string $points_type [ common consolation]
     */
    public function fetchRandomPoints($points_type)
    {
        if(! isset( $this->configs[$points_type] ) )
        {
            throw new \Exception( 'points strategy path for ' .var_export($points_type, true). ' is not configured in '. var_export( $this->configs, true) );
        }

        $pointsPool = new PointsPool( $this->configs[$points_type]['points_pool']
            ,$this->configs[$points_type]['points_strategy'] );
        // buildPointsPool
        return $pointsPool->fetchByRandom();
    }

    /**
     * for cron command 
     * @param integer $duration  days after order pending
     * @return null
     */
    public function finishAudit($duration ) 
    {
        $logger = $this->logger;
        $em = $this->em;

        // fetch records
        $eggs_info_by_user = $em->getRepository('JiliFrontendBundle:GameEggsBreakerTaobaoOrder')->fetchPendingOnCron($duration);
        
        foreach($eggs_info_by_user as $user_id => $eggs_info) {
            // caculate eggs
            $eggsInfo = $em->getRepository('JiliFrontendBundle:GameEggsBreakerEggsInfo')
                ->findOneOrCreateByUserId($user_id);

            $token = $eggsInfo->getToken();
            $result_caculated  = TaobaoOrderToEggs::caculateEggs( $eggsInfo->getOffcutForNext() + $eggs_info['total_paid'] );

            try {
                $em->getConnection()->beginTransaction();
                $eggsInfo->updateNumOfEggs(array(
                    'offcut'=> $result_caculated['left'],
                    'common'=> $result_caculated['count_of_eggs'],
                    'consolation'=> $eggs_info['count_of_uncertain'])
, $token)
                    ->refreshToken();

                // userId , eggsInfo  
                foreach($eggs_info['entities_to_update'] as $entity ) {
                    $entity->finishAudit();
                }
                $em->flush();
                $em->getConnection()->commit();
            } catch(\Exception $e) {
                $logger->crit('[backend][finishAudit]'. $e->getMessage());
                $em->getConnection()->rollback();
            }
        }
    }


    /**
     *  审核第1轮的即时订单
     * @param GameEggsBreakerTaobaoOrder $order
     */
    public function auditOrderEntity(GameEggsBreakerTaobaoOrder $order) 
    {
        $logger = $this->logger;
        $em = $this->em;
        $order->finishAudit();
        if( ! $order->isInvalid()) {
            // caculate eggs info
            $total_paid = 0;
            $count_of_uncertain = 0;
            // fetch previous or create eggsInfo
            $eggsInfo = $em->getRepository('JiliFrontendBundle:GameEggsBreakerEggsInfo')
                ->findOneOrCreateByUserId($order->getUserId());
            $token = $eggsInfo->getToken();

            if($order->isValid()) {
                $total_paid = $order->getOrderPaid() + $eggsInfo->getTotalPaid();
                $result_caculated  = TaobaoOrderToEggs::caculateEggs( $total_paid );

                $eggsInfo->updateNumOfEggs(array('paid'=>$total_paid,
                    'common'=> $result_caculated['count_of_eggs']),
                $token);
            } elseif( $order->isUncertain()) {
                $eggsInfo->updateNumOfEggs(array('consolation'=> 1), $token);
            }
            $eggsInfo->refreshToken();
        }

        try {
            $em->getConnection()->beginTransaction();
            $em->flush();
            $em->getConnection()->commit();
        } catch(\Exception $e) {
            $logger->crit('[backend][auditOrder]'. $e->getMessage());
            $em->getConnection()->rollback();
        }
    }

    /**
     *  审核第2轮的即时订单
     * @param GameEggsBreakerTaobaoOrder $order
     */
    public function auditImmdiateOrderEntity(GameEggsBreakerTaobaoOrder $order) 
    {
        $logger = $this->logger;
        $em = $this->em;
        $order->finishAudit();
        if( ! $order->isInvalid()) {
            // caculate eggs info
            $total_paid = 0;
            $count_of_uncertain = 0;

            // fetch previous or create eggsInfo
            $eggsInfo = $em->getRepository('JiliFrontendBundle:GameEggsBreakerEggsInfo')
                ->findOneOrCreateByUserId($order->getUserId());
            $token = $eggsInfo->getToken();
            if($order->isValid()) {
                $total_paid = $order->getOrderPaid() + $eggsInfo->getTotalPaid();
                $cost_per_egg = $this->configs['immediate_egg_cost'];
                $result_caculated = TaobaoOrderToEggs::caculateImmediateEggs($order->getOrderPaid(),$eggsInfo->getOffcutForNext(),$cost_per_egg );

                $eggsInfo->updateNumOfEggs(array('paid'=>$total_paid,
                    'common'=> $eggsInfo->getNumOfCommon() + $result_caculated['count_of_eggs'],
                    'offcut'=> $cost_per_egg - $result_caculated['left']
                ),
                $token);

            } elseif( $order->isUncertain()) {
                $eggsInfo->updateNumOfEggs(array('consolation'=> 1), $token);

            }

            $eggsInfo->refreshToken();

            // 发送站内信。 
            $uid =$order->getUserId();
            $em->getRepository('JiliApiBundle:SendMessage0'. ($uid % 10))->insertSendMs( array(
                  'userid' => $uid,
                  'title' => '订单审核结果',
                  'content' => '您提交的订单已审核成功，<a href="'.$this->router->generate('jili_frontend_decemberactivity_index').'" class="corLightRed">查看金蛋</a>'
            )); 
        }

        try {
            $em->getConnection()->beginTransaction();
            $em->flush();
            $em->getConnection()->commit();
        } catch(\Exception $e) {
            $logger->crit('[backend][auditOrder]'. $e->getMessage());
            $em->getConnection()->rollback();
        }
    }

    public function fetchSentStat()
    {
        $file = $this->configs ['sent_stat'] ;  
        $js = new JsonCacheFileHandler();
        $content = $js->readCached($file);
        // if not exists or latest exists
        if(is_null( $content ) || empty($content) ) {
            $this->writeSentStat();
            $content = $js->readCached($file);
        } else {
            $ts = $this->em->getRepository('JiliFrontendBundle:GameEggsBreakerTaobaoOrder')->getLastestTimestampEgged();
            if( ! is_null($ts) && strtotime($ts) > strtotime($content['ts'])) {
                $this->writeSentStat();
                $content = $js->readCached($file);
            }
        }
        return $content;
    }

    public function writeSentStat()
    {
        $file = $this->configs ['sent_stat'] ;  
        $js = new JsonCacheFileHandler();
        // fetch data to write 
        $data = array(
            'ts' => $this->em->getRepository('JiliFrontendBundle:GameEggsBreakerTaobaoOrder')->getLastestTimestampEgged(),
            'eggsInfo' => $this->em->getrepository('JiliFrontendBundle:GameEggsBreakerTaobaoOrder')
            ->findLatestEggedNickList( 10 ));
        $js->writeCache($data, $file);
    }


    public function fetchBrokenStat()
    {
        $file = $this->configs ['broken_stat'] ;  
        $js = new JsonCacheFileHandler();
        $content = $js->readCached($file);
        // if not exists or latest exists
        if(is_null( $content ) || empty($content) ) {
            $this->writeBrokenStat();
            $content = $js->readCached($file);
        } else {
            $ts = $this->em->getRepository('JiliFrontendBundle:GameEggsBrokenLog')->getLastestTimestampBroken();
            if( ! is_null($ts) && strtotime($ts) > strtotime($content['ts'])) {
                $this->writeBrokenStat();
                $content = $js->readCached($file);
            }
        }
        return $content;
    }

    public function writeBrokenStat()
    {
        $file = $this->configs ['broken_stat'] ;  
        $js = new JsonCacheFileHandler();
        // fetch data to write 
        $data = array(
            'ts' => $this->em->getRepository('JiliFrontendBundle:GameEggsBrokenLog')->getLastestTimestampBroken(),
            'breakersInfo' => $this->em->getrepository('JiliFrontendBundle:GameEggsBrokenLog')
            ->findLatestBrokenNickList( 10 ));
        $js->writeCache($data, $file);
    }

    /**
     * @param array $params array('user_id', 'token', 'egg_type' )
     */
    public function breakEgg( $params)
    {
        // verify the  token 
        if( ! isset($params['token']) || strlen( $params['token']) !== GameEggsBreakerEggsInfo::TOKEN_LENGTH ) {
            return array('code'=> 1); // invalid token 
        }

        $em = $this->em;
        $user_id = $params['user_id'];
        $token = $params['token'];

        $eggsInfo = $em->getRepository('JiliFrontendBundle:GameEggsBreakerEggsInfo')
            ->findOneBy(array(
                'userId'=> $user_id,
                'token'=>$token,
            ));

        try {
            $em->getConnection()->beginTransaction();

            $egg_type  = $eggsInfo->getEggTypeByRandom();
            if($egg_type === GameEggsBreakerEggsInfo::EGG_TYPE_COMMON) {
                $points_fetched = $this->fetchRandomPoints('common');
            } elseif ($egg_type === GameEggsBreakerEggsInfo::EGG_TYPE_CONSOLATION) {
                $points_fetched = $this->fetchRandomPoints('consolation');
            } else {
                return ;
            }
           // $points_fetched 是直接从奖池中取得的值 
            // 如果为正数,直接用于米粒数;否则为特定的奖蛋。
            if($points_fetched > 0 ) {
                $points = $points_fetched;

                $eggsInfo->reduceCountOfEgg($egg_type);

                $ad_id = AdCategory::ID_GAME_EGGS_BREAKER; // 31
                $adCategory = $em->getRepository('JiliApiBundle:AdCategory')
                    ->findOneById($ad_id); 

                // insert task_history
                $em->getRepository('JiliApiBundle:TaskHistory00')
                    ->init( array(
                        'userid' => $user_id, 
                        'orderId' => 0 ,
                        'taskType' =>   \Jili\ApiBundle\Entity\TaskHistory00::TASK_TYPE_GAME_EGGS_BREAKER,
                        'categoryType' => $ad_id,
                        'task_name' => $adCategory->getDisplayName(),
                        'point' => $points,
                        'date' => new \Datetime(),
                        'status' => 1
                    ));

                // insert point_history
                $em->getRepository('JiliApiBundle:PointHistory00')
                    ->get( array(
                        'userid' => $user_id,
                        'point' => $points,
                        'type' =>  $ad_id
                    ));

                // update user.point更新user表总分数
                $user = $em->getRepository('JiliApiBundle:User')->find($user_id);
                $oldPoint = $user->getPoints();
                $user->setPoints(intval($oldPoint+$points));
                // 不是再来1次金蛋。
                $is_once_more = false;
            } else {
                $points = 0;
                if($points_fetched === $this->configs['strategy_value_of_once_more'] ) {
                    $egg_type = $eggsInfo->getEggTypeOfOnceMore($egg_type);
                    // 是再来1次金蛋。
                    $is_once_more = true;
                }
            }

            // insert  daily_log
            $em->getRepository('JiliFrontendBundle:GameEggsBrokenLog')
                ->addLog(array(
                    'eggType' => $egg_type,
                    'points' =>  $points,
                    'userId' => $user_id,
                ));

            $em->flush();
            $em->getConnection()->commit();
            $em->clear();

            if( $points_fetched >= 0) {
                return array('code'=> 0, 'data'=> array('points'=>$points ));
            }

            return array('code'=> 0, 'data'=> array('points'=>$points, 'is_once_more'=>$is_once_more ));
        } catch(\Exception $e) {
            // internal error
            $logger->crit('[backend][breakEgg]'. $e->getMessage());
            $em->getConnection()->rollback();
        }
        return  ;  
    }

    public function setEntityManager(EntityManager $em)
    {
        $this->em= $em;
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
        return $this;
    }

    public function setRouter($router)
    {
        $this->router = $router;
        return $this;
    }
}
