<?php

namespace Jili\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Jili\ApiBundle\Utility\SequenseEntityClassFactory;
/**
 * @Route("/game-seeker")
 */
class GameSeekerController extends Controller /* implements signedInRequiredInterface,  ajaxRequiredInterface */
{

    /**
     * @return {code: CODE, msg: "", data: {countOfChest: Num , 'token': '32string'}}
     *  CODE: 0, 此用户今天还没有寻宝
     *  CODE: 1, 此用户今天已经完成寻宝
     *         {} , when not Ajax request or not signed in. 
     * @Route("/getChestInfo")
     * @Method("POST");
     **/
    function getChestInfoAction() 
    {
        $logger = $this->get('logger');
        $response = new JsonResponse();
        $request = $this->get('request');
        if ( !$request->isXmlHttpRequest()) {
            return $response;
        }

        // get session uid.
        if( ! $this->get('session')->has('uid')) {
            $logger->debug('{jarod}'. implode(':', array(__LINE__, __FILE__, '')));
            return $response;
        }
        $uid = $this->get('session')->get('uid');
        $logger->debug('{jarod}'. implode(':', array(__LINE__, __FILE__, '')). var_export($uid, true));
        $em  = $this->get('doctrine.orm.entity_manager');
//        $pointHistoryRepository = SequenseEntityClassFactory::createInstance('PointHistory', $uid);
        $is_completed = $em->getRepository('JiliApiBundle:PointHistory01')->isGameSeekerCompletedToday($uid);
        if(  $is_completed) {
            $response->setData(array( 'code'=> 1));
            return $response;
        }

        $gameInfo = $em->getRepository('JiliFrontendBundle:GameSeekerDaily')->getInfoByUser( $uid);

        $logger->debug('{jarod}'. implode(':', array(__LINE__, __FILE__, '')). var_export($gameInfo, true));
        // todo: manage the countOfChest in admin page
        // applied_at\publish_at, table system_configurations
        // read in to a cache file.
        // fetch from the cache file.
        //
        $response->setData(array( 'code'=> 0, 'data'=> array('countOfChest'=> 3, 'token'=> $gameInfo->getToken()) ));
        return $response;
    }

    /**
     * 用户点到宝箱
     * request:  token
     * return:  { code: CODE, message: "" , data {points: Num } }
     *    CODE 取值:
     *    0: 寻宝成功
     *    1: 奖品过期,Thank you.
     * @Route("/click")
     * @Method("POST")
     */
    function clickAction() 
    {
        $response = new JsonResponse();
        $request = $this->getRequest();
        if ( !$request->isXmlHttpRequest()) {
            return $response;
        }

        $session = $this->get('session');
        // get session uid.
        if( !$session->has('uid') ){
            return $response;
        }

        // todo: fetch point from the pool by service 
        // todo: confirm the point from the pool by service 
        
        $em = $this->getDoctrine()->getEntityManager();
        $conn  = $em->getConnection();

        // transaction!
        
        // insert task_history
        // insert point_history
        // update game_seeker_daily
        // update user.point
        // commit
        
        // update session of task_list.
        // update session of user.points 
    }
}
