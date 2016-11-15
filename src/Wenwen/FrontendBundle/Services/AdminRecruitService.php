<?php

namespace Wenwen\FrontendBundle\Services;

use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;

/**
 * AdminSurvey
 */
class AdminRecruitService
{
    private $logger;

    private $em;

    private $parameterService;


    public function __construct(LoggerInterface $logger,
                                EntityManager $em,
                                ParameterService $parameterService)
    {
        $this->logger = $logger;
        $this->em = $em;
        $this->parameterService = $parameterService;
    }

    public function getDailyReport($from = null, $to = null){
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
        
        return $return;
    }

    public function getMonthlyReport($from = null, $to = null){
        if(empty($from)){
            $from = (new \DateTime())->sub(new \DateInterval('P6M'))->setTime(0,0,0); 
        }
        if(empty($to)){
            $to = (new \DateTime())->setTime(0,0,0);
        }

        $this->logger->debug('user_track from=' . $from->format('Y-m-d 00:00:00'));
        $this->logger->debug('user_track to=' . $to->format('Y-m-d 00:00:00'));

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
        
        return $return;
    }


}
