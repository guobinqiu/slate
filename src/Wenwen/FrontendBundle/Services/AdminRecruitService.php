<?php

namespace Wenwen\FrontendBundle\Services;

use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;
use Predis\Client;
use Wenwen\FrontendBundle\ServiceDependency\CacheKeys;

/**
 * 招募的统计信息
 */
class AdminRecruitService
{
    private $logger;

    private $em;

    private $parameterService;

    private $redis;


    public function __construct(LoggerInterface $logger,
                                EntityManager $em,
                                ParameterService $parameterService,
                                Client $redis)
    {
        $this->logger = $logger;
        $this->em = $em;
        $this->parameterService = $parameterService;
        $this->redis = $redis;
    }

    public function getDailyReport($from = null, $to = null){
        $val = $this->redis->get(CacheKeys::ADMIN_RECRUIT_REPORT_DAILY);
        if(!empty($val)){
            $this->logger->debug(__METHOD__ . ' Found daily report in redis. ' . $val);
            return json_decode($val, true);
        }

        if(empty($from)){
            $from = (new \DateTime())->sub(new \DateInterval('P90D'))->setTime(0,0,0); 
        }
        if(empty($to)){
            $to = (new \DateTime())->setTime(0,0,0);
        }

        // 统计的前一天的0点作为判断活跃的开始时间点
        $activeAt = (new \DateTime())->sub(new \DateInterval('P1D'))->setTime(0,0,0); 

        $results = $this->em->getRepository('WenwenFrontendBundle:User')->getRecruitRouteDailyCount($from, $to);

        $actives = $this->em->getRepository('WenwenFrontendBundle:User')->getActiveRouteDailyCount($from, $to, $activeAt);

        $this->logger->debug(__METHOD__ . ' registers: ' . json_encode($results));
        $this->logger->debug(__METHOD__ . ' actives:   ' . json_encode($actives));

        $recruitRouteTitles = array();
        $recruitRouteTitles['total'] = 0;


        // 补全所有出现的recruitRoute名称
        foreach($results as $result){
            $date = $result['date'];
            $recruitRoute = $result['recruit_route'];
            $count = $result['count'];

            if(!array_key_exists($recruitRoute, $recruitRouteTitles)){
                // 记录下所有出现的recruitRoute名称
                $recruitRouteTitles[$recruitRoute] = 0;
            }
        }

        $rawActives = array();

        foreach($results as $result){
            $date = $result['date'];
            $recruitRoute = $result['recruit_route'];
            $count = $result['count'];
            if(!array_key_exists($date, $rawActives)){
                $rawActives[$date] = array();
            }
            $rawActives[$date][$recruitRoute] = $count;
        }

        $registers = array();
        foreach($rawActives as $date => $recruitRoutes){
            $registers[$date] = array();
            $registers[$date]['total'] = 0;

            // 补全每个日期的所有route的注册数，并且按照title的顺序排好
            foreach($recruitRouteTitles as $title => $value){
                if(!array_key_exists($title, $recruitRoutes)){
                    $registers[$date][$title] = 0;
                } else {
                    $registers[$date][$title] = $recruitRoutes[$title];
                    $registers[$date]['total'] += $recruitRoutes[$title];
                }
            }
        }

        // 增加活跃信息
        $reports = array();
        foreach($registers as $date => $registerRoutes){
            $this->logger->debug(__METHOD__ . ' date=' . $date . ' ' . json_encode($registerRoutes));
            $reports[$date] = array();

            $tmpActive = array();
            $tmpActive['total'] = 0;
            foreach($actives as $active){
                if($date == $active['date']){
                    //$this->logger->debug(__METHOD__ . ' found ' . json_encode($active));
                    $tmpActive[$active['recruit_route']] = $active['count'];
                    $tmpActive['total'] += $active['count'];
                }
            }
            
            foreach($registerRoutes as $title => $value){
                if(array_key_exists($title, $tmpActive)){
                    $reports[$date][$title]['registerCount'] = $value;
                    $reports[$date][$title]['activeCount'] = $tmpActive[$title];
                    $reports[$date][$title]['rawRetentionRate'] = round($tmpActive[$title] / $value * 100, 2);
                    $reports[$date][$title]['retentionRate'] = sprintf("%02.2f", round($tmpActive[$title] / $value * 100, 2));
                    $reports[$date][$title]['text'] = $tmpActive[$title] . '/'. $value . '(' . $reports[$date][$title]['retentionRate'] . '%)';
                    //$reports[$date][$title] = $tmpActive[$title] . '/'. $value . '(' . round($tmpActive[$title] / $value * 100, 2). '%)';
                } else {
                    $reports[$date][$title]['registerCount'] = $value;
                    $reports[$date][$title]['activeCount'] = 0;
                    $reports[$date][$title]['rawRetentionRate'] = 0;
                    $reports[$date][$title]['retentionRate'] = '00.00';
                    $reports[$date][$title]['text'] =  '0/'. $value . '(00.00%)';
                    //$reports[$date][$title] = '0/'. $value . '(0%)';
                }
            }

        }

        $return = array();
        $return['reports'] = $reports;
        $return['titles'] = $recruitRouteTitles;

        $this->redis->set(CacheKeys::ADMIN_RECRUIT_REPORT_DAILY, json_encode($return));
        $this->redis->expire(CacheKeys::ADMIN_RECRUIT_REPORT_DAILY, CacheKeys::ADMIN_RECRUIT_REPORT_DAILY_TIMEOUT);
        $this->logger->debug(__METHOD__ . ' Found monthly report in db. ' . json_encode($return));
        return $return;
    }

    public function getMonthlyReport($from = null, $to = null){
        $val = $this->redis->get(CacheKeys::ADMIN_RECRUIT_REPORT_MONTHLY);
        if(!empty($val)){
            $this->logger->debug(__METHOD__ . ' Found monthly report in redis. ' . $val);
            return json_decode($val, true);
        }

        if(empty($from)){
            $from = (new \DateTime())->sub(new \DateInterval('P6M'))->setTime(0,0,0); 
        }
        if(empty($to)){
            $to = (new \DateTime())->setTime(0,0,0);
        }

        // 统计的前一天的0点作为判断活跃的开始时间点
        $activeAt = (new \DateTime())->sub(new \DateInterval('P1M'))->setTime(0,0,0); 

        $results = $this->em->getRepository('WenwenFrontendBundle:User')->getRecruitRouteMonthlyCount($from, $to);
        $actives = $this->em->getRepository('WenwenFrontendBundle:User')->getActiveRouteMonthlyCount($from, $to, $activeAt);

        $recruitRouteTitles = array();
        $recruitRouteTitles['total'] = 0;

        // 补全所有出现的recruitRoute名称
        foreach($results as $result){
            $date = $result['date'];
            $recruitRoute = $result['recruit_route'];
            $count = $result['count'];

            if(!array_key_exists($recruitRoute, $recruitRouteTitles)){
                // 记录下所有出现的recruitRoute名称
                $recruitRouteTitles[$recruitRoute] = 0;
            }
        }


        $rawActives = array();

        foreach($results as $result){
            $date = $result['date'];
            $recruitRoute = $result['recruit_route'];
            $count = $result['count'];
            if(!array_key_exists($date, $rawActives)){
                $rawActives[$date] = array();
            }
            $rawActives[$date][$recruitRoute] = $count;
        }

        $registers = array();
        foreach($rawActives as $date => $recruitRoutes){
            $registers[$date] = array();
            $registers[$date]['total'] = 0;

            // 补全每个日期的所有route的注册数，并且按照title的顺序排好
            foreach($recruitRouteTitles as $title => $value){
                if(!array_key_exists($title, $recruitRoutes)){
                    $registers[$date][$title] = 0;
                } else {
                    $registers[$date][$title] = $recruitRoutes[$title];
                    $registers[$date]['total'] += $recruitRoutes[$title];
                }
            }
        }

        // 增加活跃信息
        $reports = array();
        foreach($registers as $date => $registerRoutes){
            $this->logger->debug(__METHOD__ . ' date=' . $date . ' ' . json_encode($registerRoutes));
            $reports[$date] = array();

            $tmpActive = array();
            $tmpActive['total'] = 0;
            foreach($actives as $active){
                if($date == $active['date']){
                    $tmpActive[$active['recruit_route']] = $active['count'];
                    $tmpActive['total'] += $active['count'];
                }
            }

            foreach($registerRoutes as $title => $value){
                if(array_key_exists($title, $tmpActive)){
                    $reports[$date][$title]['registerCount'] = $value;
                    $reports[$date][$title]['activeCount'] = $tmpActive[$title];
                    $reports[$date][$title]['rawRetentionRate'] = round($tmpActive[$title] / $value * 100, 2);
                    $reports[$date][$title]['retentionRate'] = sprintf("%02.2f", round($tmpActive[$title] / $value * 100, 2));
                    $reports[$date][$title]['text'] = $tmpActive[$title] . '/'. $value . '(' . $reports[$date][$title]['retentionRate'] . '%)';
                    //$reports[$date][$title] = $tmpActive[$title] . '/'. $value . '(' . round($tmpActive[$title] / $value * 100, 2). '%)';
                } else {
                    $reports[$date][$title]['registerCount'] = $value;
                    $reports[$date][$title]['activeCount'] = 0;
                    $reports[$date][$title]['rawRetentionRate'] = 0;
                    $reports[$date][$title]['retentionRate'] = '00.00';
                    $reports[$date][$title]['text'] =  '0/'. $value . '(00.00%)';
                    //$reports[$date][$title] = '0/'. $value . '(0%)';
                }
            }

        }

        $return = array();
        $return['reports'] = $reports;
        $return['titles'] = $recruitRouteTitles;

        $this->redis->set(CacheKeys::ADMIN_RECRUIT_REPORT_MONTHLY, json_encode($return));
        $this->redis->expire(CacheKeys::ADMIN_RECRUIT_REPORT_MONTHLY, CacheKeys::ADMIN_RECRUIT_REPORT_MONTHLY_TIMEOUT);

        $this->logger->debug(__METHOD__ . ' Found monthly report in db. ' . json_encode($return));
        return $return;
    }


}
