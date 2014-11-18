<?php

namespace Jili\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @Route("/game-seeker")
 */
class GameSeekerController extends Controller
{

    /**
     * @return {code: CODE, msg: "", data: {countOfChest: Num , 'token': '16位string'}}
     *  CODE: 0, 此用户今天还没有寻宝
     *  CODE: 1, 此用户今天已经完成寻宝
     * @Route("/getChestInfo")
     * @Method("POST");
     **/
    function getChestInfoAction() 
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
        $uid = $session->get('uid');
        // check whether current user has done.
        $em  = $this->getEntityManager();
        $pointHistoryRepository = SequenseEntityClassFactory::createInstance('PointHistory', $uid);
        $is_completed = $pointHistoryRepository->isGameSeekerCompletedToday($uid);
        if( ! $is_completed) {
            // insert/update  game_seeker_daily
            $gameInfo = $em->getRepository('JiliFrontendBundle:GameSeekerDaily')->getInfoByUser( array('userId'=>$uid, ''=>));
        }
// 

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
        // transaction!
        // insert task_history
        // insert point_history
        // update game_seeker_daily
        // update user.point
        // update session of task_list.
    }
}
