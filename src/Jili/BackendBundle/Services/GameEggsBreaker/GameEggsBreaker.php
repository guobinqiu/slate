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
        $logger->debug('{jarod}'. implode(':' , array(__LINE__, __FILE__, '')));
        $logger->debug('{jarod}'. implode(':' , array(__LINE__, __FILE__, '$duration:')). var_export($duration, true));

        // fetch records
        $eggs_info_by_user = $em->getRepository('JiliFrontendBundle:GameEggsBreakerTaobaoOrder')->fetchPendingOnCron($duration);
        
        foreach($eggs_info_by_user as $user_id => $eggs_info) {
            $logger->debug('{jarod}'. implode(':' , array(__LINE__, __FILE__, '$eggs_info["total_paid"]:')). var_export($eggs_info['total_paid'], true));
            $logger->debug('{jarod}'. implode(':' , array(__LINE__, __FILE__, '$eggs_info["count_of_uncertain"]:')). var_export($eggs_info['count_of_uncertain'], true));
            $logger->debug('{jarod}'. implode(':' , array(__LINE__, __FILE__, 'count($eggs_info["entities_to_update"]):')). var_export(count($eggs_info['entities_to_update']), true));
                // caculate eggs
            $eggsInfo = $em->getRepository('JiliFrontendBundle:GameEggsBreakerEggsInfo')
                ->findOneOrCreateByUserId($user_id);

            $token = $eggsInfo->getToken();
            $result_caculated  = TaobaoOrderToEggs::caculateEggs( $eggsInfo->getOffcutForNext() + $eggs_info['total_paid'] );

 //           $em->clear();
            try {
                $em->getConnection()->beginTransaction();
                //$logger->debug('{jarod}'. implode(':' , array(__LINE__, __FILE__, '$eggs:')).var_export($eggs, true));
                $eggsInfo->updateNumOfEggs(array(
                    'offcut'=> $result_caculated['left'],
                    'common'=> $result_caculated['count_of_eggs'],
                    'consolation'=> $eggs_info['count_of_uncertain'])
, $token)
                    ->refreshToken();

                $logger->debug('{jarod}'. implode(':' , array(__LINE__, __FILE__, '')));
                //$em->persist($eggsInfo);
                // userId , eggsInfo  
                foreach($eggs_info['entities_to_update'] as $entity ) {
                    $entity->finishAudit();
//                    $em->persist($entity);
                }
                $em->flush();
                //$em->clear();
                $em->getConnection()->commit();
            } catch(\Exception $e) {
                $logger->crit('[backend][finishAudit]'. $e->getMessage());
                $em->getConnection()->rollback();
                //todo: add context
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

        if( !  $order->isInvalid()) {
            // caculate eggs info
            $total_paid = 0;
            $count_of_uncertain = 0;
            $this->logger->debug('{jarod}'. implode(':' , array(__LINE__, __FILE__, '$order->getOrderPaid():')). var_export($order->getOrderPaid(), true));
            // fetch previous or create eggsInfo
            $eggsInfo = $em->getRepository('JiliFrontendBundle:GameEggsBreakerEggsInfo')
                ->findOneOrCreateByUserId($order->getUserId());
            $token = $eggsInfo->getToken();

            if($order->isValid()) {
                $total_paid = $order->getOrderPaid() + $eggsInfo->getOffcutForNext();
                $result_caculated  = TaobaoOrderToEggs::caculateEggs(  $total_paid );

                $this->logger->debug('{jarod}'. implode(':' , array(__LINE__, __FILE__, '$total_paid:')). var_export($total_paid, true));
                $this->logger->debug('{jarod}'. implode(':' , array(__LINE__, __FILE__, '$result_caculated:')). var_export($result_caculated, true));
                $eggsInfo->updateNumOfEggs(array(
                    'offcut'=> $result_caculated['left'],
                    'common'=> $result_caculated['count_of_eggs'],
                    'consolation'=> 0)
                    , $token);
            } elseif( $order->isUncertain()) {
                $eggsInfo->updateNumOfEggs(array(
                    'common'=> 0,
                    'consolation'=> 1)
                    , $token);
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
     *
     */
    public function breakEgg()
    {
        $this->logger->debug('{jarod}'. implode(':' , array(__LINE__, __FILE__, '')));
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
