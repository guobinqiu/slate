<?php

namespace Jili\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Jili\ApiBundle\Entity\AdCategory;

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
            return $response;
        }
        $userId = $this->get('session')->get('uid');
        $logger->debug('{jarod}'. implode(':', array(__LINE__, __FILE__, '')). var_export($userId, true));
        $em  = $this->get('doctrine.orm.entity_manager');
        $is_completed = $em->getRepository('JiliApiBundle:PointHistory0'. ($userId % 10) )->isGameSeekerCompletedToday($userId);
        if(  $is_completed) {
            $response->setData(array( 'code'=> 1));
            return $response;
        }
        $logger->debug('{jarod}'. implode(':', array(__LINE__, __FILE__, '')). var_export($is_completed, true));

        $gameInfo = $em->getRepository('JiliFrontendBundle:GameSeekerDaily')->getInfoByUser( $userId);

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
     *    {}  when not ajax request; no 32byte token post, not signin.
     * @Route("/click")
     * @Method("POST")
     */
    function clickAction() 
    {
        $logger = $this->get('logger');
        $response = new JsonResponse();
        $request = $this->getRequest();

        if ( !$request->isXmlHttpRequest()) {
        $logger->debug('{jarod}'. implode(':', array(__LINE__, __FILE__, '')) );
            return $response;
        }
        $token = $request->request->get('token');
        if(  strlen($token) !== 32) {
        $logger->debug('{jarod}'. implode(':', array(__LINE__, __FILE__, '')) );
            return $response;
        }

        $logger->debug('{jarod}'. implode(':', array(__LINE__, __FILE__, '')). var_export($token, true) );

        // get session uid.
        if( ! $this->get('session')->has('uid') ){
        $logger->debug('{jarod}'. implode(':', array(__LINE__, __FILE__, '')) );
            return $response;
        }

        $userId = $this->get('session')->get('uid');
        $em = $this->get('doctrine.orm.entity_manager');
$connection = $this->get('database_connection');
        $logger->debug('{jarod}'. implode(':', array(__LINE__, __FILE__, '')). var_export($userId, true));
        // todo: fetch point from the pool by service 
        // todo: confirm the point from the pool by service 
        
        $is_completed = $em->getRepository('JiliApiBundle:PointHistory0'. ($userId % 10) )->isGameSeekerCompletedToday($userId);
        if(  $is_completed) {
            $response->setData(array( 'code'=> 1/*, 'message'=>'寻宝箱已经完成'*/));
            return $response;
        }
        $gameSeekerDaily = $em->getRepository('JiliFrontendBundle:GameSeekerDaily')->findOneBy(array('token'=> $token,'userId'=> $userId));
        if(!  $gameSeekerDaily ) {
            return $response;
        }

        $adId = AdCategory::ID_GAME_SEEKER; // 30
        $adCategory = $em->getRepository('JiliApiBundle:AdCategory')->findOneById($adId); 
        // $conn  = $this->get('doctrine.dbal.default_connection');
        $points = 1;

        // transaction!
        // $em instanceof EntityManager
        $em->getConnection()->beginTransaction(); // suspend auto-commit
        try {
            // insert task_history
            $task_params = array(
                'userid' => $userId, 
                'orderId' => 0 ,
                'taskType' =>   \Jili\ApiBundle\Entity\TaskHistory00::TASK_TYPE_GAME_SEEKER,
                'categoryType' => $adId,
                'task_name' => $adCategory->getDisplayName(),
                'point' => $points,
                'date' => date_create(),
                'status' => 1
            );

            $this->get('general_api.task_history')->init($task_params  );

            // insert point_history
            $points_params = array(
                'userid' => $userId,
                'point' => $points,
                'type' =>  $adId
            );
            $this->get('general_api.point_history')->get($points_params  );

            // update game_seeker_daily
            $gameSeekerDaily->setClickedDay(new \DateTime())
                ->setPoints($points);
            $em->persist($gameSeekerDaily);
            $em->flush();

            // update user.point更新user表总分数
            $user = $em->getRepository('JiliApiBundle:User')->find($userId);
            $oldPoint = $user->getPoints();
            $user->setPoints(intval($oldPoint+$points));
            $em->persist($user);
            $em->flush();

            $em->getConnection()->commit();
        } catch (\Exception $e) {
            $em->getConnection()->rollback();
            throw $e;
        }

            print_r(get_class_methods($this->get('logger')));
        // update session of task_list.
        // update session of user.points 确认中的米粒数
        $this->get('login.listener')->updatePoints($user->getPoints() );
        $response->setData(array( 'code'=> 0, 'message'=>'寻宝箱成功', 'data'=> array('points'=>$points) ));
        return $response;
    }
}
