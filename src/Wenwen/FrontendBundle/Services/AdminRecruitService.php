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
            $from = (new \DateTime())->sub(new \DateInterval('P30D'))->setTime(0,0,0); 
        }
        if(empty($to)){
            $to = (new \DateTime())->setTime(0,0,0);
        }

        $results = $this->em->getRepository('WenwenFrontendBundle:User')->getRecruitRouteDailyCount($from, $to);

        

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


        $rawReports = array();

        foreach($results as $result){
            $date = $result['date'];
            $recruitRoute = $result['recruit_route'];
            $count = $result['count'];
            if(!array_key_exists($date, $rawReports)){
                $rawReports[$date] = array();
            }
            $rawReports[$date][$recruitRoute] = $count;
        }

        $reports = array();
        foreach($rawReports as $date => $recruitRoutes){
            $reports[$date] = array();
            $reports[$date]['total'] = 0;

            // 补全每个日期的所有route的注册数，并且按照title的顺序排好
            foreach($recruitRouteTitles as $title => $value){
                if(!array_key_exists($title, $recruitRoutes)){
                    $reports[$date][$title] = 0;
                } else {
                    $reports[$date][$title] = $recruitRoutes[$title];
                    $reports[$date]['total'] += $recruitRoutes[$title];
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

        $results = $this->em->getRepository('WenwenFrontendBundle:User')->getRecruitRouteMonthlyCount($from, $to);

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


        $rawReports = array();

        foreach($results as $result){
            $date = $result['date'];
            $recruitRoute = $result['recruit_route'];
            $count = $result['count'];
            if(!array_key_exists($date, $rawReports)){
                $rawReports[$date] = array();
            }
            $rawReports[$date][$recruitRoute] = $count;
        }

        $reports = array();
        foreach($rawReports as $date => $recruitRoutes){
            $reports[$date] = array();
            $reports[$date]['total'] = 0;

            // 补全每个日期的所有route的注册数，并且按照title的顺序排好
            foreach($recruitRouteTitles as $title => $value){
                if(!array_key_exists($title, $recruitRoutes)){
                    $reports[$date][$title] = 0;
                } else {
                    $reports[$date][$title] = $recruitRoutes[$title];
                    $reports[$date]['total'] += $recruitRoutes[$title];
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
