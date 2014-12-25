<?php
namespace Jili\BackendBundle\Services\GameEggsBreaker;
          ;


use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Jili\BackendBundle\Utility\PointsStrategy;
use Jili\BackendBundle\Utility\PointsPool;
use Jili\BackendBundle\Utility\TaobaoOrderToEggs;
use Jili\FrontendBundle\Entity\GameEggsBreakerTaobaoOrder;
use Jili\BackendBundle\Utility\JsonCacheFileHandler;

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
// caculat eggs ...
        // loop  by userId
        //      init eggsinfo if not exists
        //  transaction 
        //      update order
        //      update eggsInfo
        //      log 
        //  next 
       
        //      update stat ( eggs sent out) cache file 
    }


    /**
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

        // update stat cache file 
        // remove last line 
        // 100 lines
        // insert fisrt line.
        
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
            $ts = $this->em->getRepository('JiliFrontendBundle:GameEggsBreakerTaobaoOrder')->getLastestTimestampeEgged();
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
            'ts' => $this->em->getRepository('JiliFrontendBundle:GameEggsBreakerTaobaoOrder')->getLastestTimestampeEgged(),
            'eggsInfo' => $this->em->getrepository('JiliFrontendBundle:GameEggsBreakerTaobaoOrder')
            ->findLatestEggedNickList( 10 ));
        $js->writeCache($data, $file);
    }

    /**
     * @param array $params array('user_id', 'token', 'egg_type' )
     */
    public function breakEgg( $params)
    {
        // token 
        //

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

}
