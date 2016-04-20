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
     * @Route("/getChestInfo", options={"expose"=true})
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

        $count_of_chest = $this->get('game_seeker.points_pool')->fetchChestCount(); 
        if( ! $this->get('session')->has('uid')) {
            $response->setData(array( 'code'=> 0, 'data'=> array('countOfChest'=> $count_of_chest, 'token'=> '' ) ));
            return $response;
        }

        $userId = $this->get('session')->get('uid');
        $em  = $this->getDoctrine()->getManager();
        
        $is_completed = $em->getRepository('JiliApiBundle:PointHistory0'. ($userId % 10) )->isGameSeekerCompletedToday($userId);
        if(  $is_completed) {
            $response->setData(array( 'code'=> 1));
            return $response;
        }

        // check the GameSeekerDaily
        $gameSeekerDaily = $em->getRepository('JiliFrontendBundle:GameSeekerDaily')->findOneBy(array('userId'=> $userId,'points'=> 0, 'clickedDay'=>new \DateTime()));
        if( !is_null($gameSeekerDaily)) {
            $response->setData(array( 'code'=> 1));
            return $response;
        }
        $gameInfo = $em->getRepository('JiliFrontendBundle:GameSeekerDaily')->getInfoByUser( $userId);
        $response->setData(array( 'code'=> 0, 'data'=> array('countOfChest'=> $count_of_chest, 'token'=> $gameInfo->getToken()) ));
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
     * @Route("/click", options={"expose"=true})
     * @Method("POST")
     */
    function clickAction() 
    {
        $logger = $this->get('logger');
        $response = new JsonResponse();
        $request = $this->getRequest();

        if ( !$request->isXmlHttpRequest()) {
            return $response;
        }

        // get session uid.
        if( ! $this->get('session')->has('uid') ){
            $this->get('session')->set('goToUrl', $this->get('router')->generate('jili_frontend_taobao_index'));
            $response->setData(array( 'code'=> 2, 'message'=>'需要登录' ));
            return $response;
        }

        $userId = $this->get('session')->get('uid');

        $token = $request->request->get('token');
        if( strlen($token) !== 32 ) {
            return $response;
        }

        $em = $this->getDoctrine()->getManager();
        // token 无效
        $gameSeekerDaily = $em->getRepository('JiliFrontendBundle:GameSeekerDaily')->findOneBy(array('token'=> $token,'userId'=> $userId,'points'=> -1, 'clickedDay'=>new \DateTime()));
        if(! $gameSeekerDaily ) {
            $response->setData(array( 'code'=> 3, 'message'=>'token过期' ));
            return $response;
        }

        $connection = $this->get('database_connection');
        
        $is_completed = $em->getRepository('JiliApiBundle:PointHistory0'. ($userId % 10) )->isGameSeekerCompletedToday($userId);
        if( $is_completed) {
            $response->setData(array( 'code'=> 1));
            return $response;
        }

        $today_day = new \DateTime();
        $today_day->setTime(0,0);
        // 已经完成的
        $gameSeekerDaily = $em->getRepository('JiliFrontendBundle:GameSeekerDaily')->findOneBy(array('userId'=> $userId, 'clickedDay'=>$today_day));

        if( ! is_null($gameSeekerDaily) && $gameSeekerDaily->getPoints() >= 0 ) {
            $response->setData(array('code'=> 1));
            return $response;
        }

        $adId = AdCategory::ID_GAME_SEEKER; // 30
        $adCategory = $em->getRepository('JiliApiBundle:AdCategory')->findOneById($adId); 
        //fetch point from the pool by service 
        $points = $this->get('game_seeker.points_pool')->fetch();
        if( $points <= 0 ) {
            // log the task has been done, update game_seeker_daily
            $gameSeekerDaily->setClickedDay(new \DateTime())
                ->setPoints(0);
            $em->persist($gameSeekerDaily);
            $em->flush();
            
            $response->setData(array( 'code'=> 0, 'message'=>'寻到一个空宝箱', 'data'=> array('points'=>0 ) ));
            return $response;
        }

        // transaction! $em instanceof EntityManager
        try {
            $em->getConnection()->beginTransaction(); // suspend auto-commit
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
            $this->get('logger')->critical('[JiliFrontend][GameSeeker][click]'. $e->getMessage());
            $this->get('session')->getFlashBag()->add('error','寻宝失败，内部出错');
            return $this->redirect($this->generateUrl('_default_error'));
        } 

        // update session of task_list.
        // update session of user.points 确认中的米粒数
        $this->get('login.listener')->updatePoints($user->getPoints() );
        $response->setData(array( 'code'=> 0, 'message'=>'寻宝箱成功', 'data'=> array('points'=>$points) ));
        return $response;
    }
    /**
     * @Route("/is-ads-visit", options={"expose"=true})
     * @Method("GET")
     */
    function isAdsVisitAction() 
    {
        $logger = $this->get('logger');
        $response = new JsonResponse();
        $request = $this->getRequest();

        if ( ! $request->isXmlHttpRequest()) {
            return $response;
        }
        // get session uid.
        if( ! $this->get('session')->has('uid')) {
            return $response;
        }

        $userId = $this->get('session')->get('uid');
        $em = $this->getDoctrine()->getManager(); 
        $result = $em->getRepository('JiliFrontendBundle:UserVisitLog')->isGameSeekerDoneDaily($userId);

        if( 1 === $result) {
            $response ->setData(array('code'=> 0, 'data'=> array('has_done'=> true) ));
        } else if( 0 ===  $result) {
            $response ->setData(array('code'=> 0, 'data'=> array('has_done'=> false ) ));
        }

        return $response;
    }
}
