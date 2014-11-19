<?php

namespace Jili\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class GameSeekerPoolController extends Controller
{
    /**
     * @Route("/build")
     * @Template()
     */
    public function buildAction()
    {
        // step1. upload a points pool  strategy
        // textarea form post
        // batch write
    }

    /**
     * @Route("/enable")
     * @Template()
     */
    public function enableAction()
    {
        // step2. confirm the latest created points strategy. set is_valid = true, update others to false 
        // update set is_valid by created_at and ids  
    }

    /**
     * @Route("/publish")
     * @Template()
     */
    public function publishAction()
    {
        //setp3. query the points strategy to cache config.
        // a cache config file app/cache_data/prod/game_seeker_pool_config_{timestamp_created).txt
    }
//
//    /**
//     * @Route("/monitor")
//     * @Template()
//     */
//    public function monitorAction()
//    {
//        // advanced usage
//        // query the game_seeker_daily.
//        // point_history_xx
//        // game_seeker_pool_YYYYMMDD_usage.txt
//        //
//    }
//
//    /**
//     * @Route("/adjust")
//     * @Template()
//     */
//    public function adjustAction()
//    {
//        // advanced usage
//    }
//
//    /**
//     * @Route("/review")
//     * @Template()
//     */
//    public function reviewAction()
//    {
//        // advanced usage
//    }
}
