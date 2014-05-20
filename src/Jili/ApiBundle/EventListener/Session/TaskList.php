<?php
namespace Jili\ApiBundle\EventListener\Session;

use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Doctrine\ORM\EntityManager;

/**
 * 
 **/
class TaskList
{
    private $em;
    private $logger;
    private $session;

    private $container;
    private $request;

    private $check_in_listener;
    private $keys;
    private $duration ;
    public function __construct( $keys, $duration)
    {
        $this->keys = $keys;
        $this->duration = $duration;
    }
    /**
     *
     */
    public function compose()
    {
        $session = $this->session;
        $logger = $this->logger;
        $em = $this->em;

        $arr = array();

        $id = $session->get('uid');
        $day=date('Ymd');

        // session life 
        $key_alive = $this->keys['alive'];

        $duration_alive = $this->duration; /* todo: add to config */
        $is_alive = false ;
        if( $session->has($key_alive)) {
            if( $duration_alive === -1 ||  time() < $duration_alive + $session->get($key_alive ) ) {
                $is_alive = true;
            } else {
                $this->reset();
            }
        } else {
            $this->reset();
        }

        unset($visit);
        unset($visit_value);

        //游戏
        $key_game =$this->keys[ 'game_visit'];
        if( $is_alive &&$session->has($key_game) ){
            $visit_value =  $session->get($key_game);
        } else {
            $visit = $em->getRepository('JiliApiBundle:UserGameVisit')->getGameVisit($id, $day);
            $visit_value = (empty ($visit))? $this->getParameter('init_one'):$arr['task']['game'] = $this->getParameter('init');
            $session->set($key_game, $visit_value);
        }

        if( isset($visit_value)) {
            $arr['task']['game'] = $visit_value;
        }

        unset($visit);
        unset($visit_value);
        //广告任务墙
        $key_adv = $this->keys['adv_visit'];
        if( $is_alive && $session->has($key_adv)) {
                $visit_value  =  $session->get($key_adv);
        } else {
            $visit = $em->getRepository('JiliApiBundle:UserAdvertisermentVisit')->getAdvertisermentVisit($id, $day);
            $visit_value = (empty ($visit))? $this->getParameter('init_one'):$arr['task']['ad'] = $this->getParameter('init');
            $session->set($key_adv, $visit_value );
        }

        if( isset( $visit_value)) {
            $arr['task']['ad'] =$visit_value ;
        }    

        unset($visit);
        unset($visit_value);
        //91wenwen
        $key_91ww = $this->keys['91ww_visit'];
        if( $is_alive&& $session->has($key_91ww) ) {
            $visit_value  =  $session->get($key_91ww);
        } else {
            $visit = $em->getRepository('JiliApiBundle:UserWenwenVisit')->getWenwenVisit($id, $day);
            $visit_value =(empty ($visit))? $this->getParameter('init_one'):$arr['task']['wen'] = $this->getParameter('init');
            $session->set($key_91ww, $visit_value);
        }

        if( isset($visit_value)) {
            $arr['task']['wen'] =  $visit_value;
        }

        unset($visit);
        unset($visit_value);

        //签到
        unset($checkin_value);
        $key_checkin = $this->keys['checkin_visit'];
        if( $is_alive&&  $session->has($key_checkin) ) {
            $checkin_value =  $session->get($key_checkin);
        } else {
            $date = date('Y-m-d');
            $checkin = $em->getRepository('JiliApiBundle:CheckinClickList')->checkStatus($id, $date);
            $checkin_value = (!empty ($checkin)) ? $this->getParameter('init'): $this->getParameter('init_one');
            $session->set($key_checkin, $checkin_value);
        }

        if( isset($checkin_value)) {
            $arr['task']['checkin'] =  $checkin_value;
        }

        //获取签到积分
        $key_checkin_point = $this->keys['checkin_point'];
        if( $is_alive && $session->has($key_checkin_point)) {
            $arr['task']['checkinPoint'] =$session->get($key_checkin_point);
        } else {
            if(isset($checkin) &&  empty ($checkin)) {
                $key_checkin_point = $this->keys['checkin_point'];
                $arr['task']['checkinPoint'] = $this->check_in_listener->getCheckinPoint( $this->request );
                $session->set($key_checkin_point, $arr['task']['checkinPoint'] );
            }
        }

        //cpa
        #        $key_cpa_visit = $this->keys['cpa_ads'];
        #        if( $is_alive && $session->has($key_cpa_visit) ) {
        #            $advertise = $session->get($key_cpa_visit);
        #        } else {
        #            $repository = $em->getRepository('JiliApiBundle:Advertiserment');
        #            $advertise = $repository->getAdvertiserListCPA($id);
        #            $session->set($key_cpa_visit, $advertise);
        #        }
        #        $arr['advertise'] = $advertise;
        #        $arr['task']['cpa'] = $arr['advertise'];

        return $arr;
    }

    public function reset() {
        $session = $this->session;
        $keys = $this->keys;

        if( isset($keys['alive'])) {
            $now =time();
            $session->set($keys['alive'], $now - $now % $this->duration );
            unset($keys['alive']);
        }

        foreach(array_keys($keys) as $key ) {
            $session->remove( $key);
        }
    }
    /**
     *    取得key对应的value
     */
    public function get($key ) {
        $keys = $this->keys;
        $session = $this->session;
        $value = null;
        if( isset($keys[$key])) {
            if( $session->has( $keys[$key] )) {
                $value = $session->get( $keys[$key] );
            }
        }
        return $value;
    }

    /**
     * 清除某个key.
     */
    public function remove($keys_to_remove = array() ) {
        $keys = $this->keys;
        $session = $this->session;
        foreach($keys_to_remove as $key) {
            if( isset($keys[$key])) {
                if( $session->has( $keys[$key] )) {
                    $session->remove( $keys[$key] );
                }
            }
        }
    }

    private function getParameter($key) {
        return $this->container->getParameter($key);
    }
    public function setSession(  $session) {
        $this->session = $session;
        return $this;
    }
    public function setLogger(  LoggerInterface $logger) {
        $this->logger = $logger;
        return $this;
    }

    public function setEntityManager( EntityManager $em) {
        $this->em= $em;
    }

    public function setContainer( $container ) {
        $this->container = $container;
        return $this;
    }
    public function setCheckInListener( $checkInListener)
    {
        $this->check_in_listener = $checkInListener;
        return $this;
    }

    public function setRequest( $request ) {
        $this->request = $request;
        return $this;
    }

}
