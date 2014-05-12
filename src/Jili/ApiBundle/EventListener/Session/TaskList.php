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
            if(  time() < $duration_alive + $session->get($key_alive ) ) {
                $is_alive = true;
            } else {
                $this->reset();
            }
        } else {
            $this->reset();
        }

        //游戏
        $key_game =$this->keys[ 'game_visit'];
        if( $is_alive && $session->has($key_game)) {
            $visit  =  $session->get($key_game);
        } else {
            $visit = $em->getRepository('JiliApiBundle:UserGameVisit')->getGameVisit($id, $day);
            $session->set($key_game, $visit);
        }

        $arr['task']['game'] =(empty ($visit))? $this->getParameter('init_one'):$arr['task']['game'] = $this->getParameter('init');

        //广告任务墙
        $key_adv = $this->keys['adv_visit'];
        if( $is_alive && $session->has($key_adv)) {
            $visit  =  $session->get($key_adv);
        } else {
            $visit = $em->getRepository('JiliApiBundle:UserAdvertisermentVisit')->getAdvertisermentVisit($id, $day);
            $session->set($key_adv, $visit);
        }

        $arr['task']['ad'] =(empty ($visit))? $this->getParameter('init_one'):$arr['task']['ad'] = $this->getParameter('init');

        //91wenwen
        $key_91ww = $this->keys['91ww_visit'];
        if( $is_alive && $session->has($key_91ww)) {
            $visit  =  $session->get($key_91ww);
        } else {
            $visit = $em->getRepository('JiliApiBundle:UserWenwenVisit')->getWenwenVisit($id, $day);
            $session->set($key_91ww, $visit);
        }

        $arr['task']['wen'] =(empty ($visit))? $this->getParameter('init_one'):$arr['task']['wen'] = $this->getParameter('init');

        //签到
        $key_checkin = $this->keys['checkin_visit'];
        if( $is_alive && $session->has($key_checkin)) {
            $visit =  $session->get($key_checkin);
        } else {
            $visit = $em->getRepository('JiliApiBundle:CheckinClickList')->checkStatus($id, $day);
            $session->set($key_checkin, $visit);
        }

        $arr['task']['checkin'] = (!empty ($visit)) ? $this->getParameter('init'): $this->getParameter('init_one');

        //获取签到积分
        if( empty ($visit)) {
            $key_checkin_point = $this->keys['checkin_point'];
            if( $is_alive && $session->has($key_checkin_point)) {
                $arr['task']['checkinPoint'] =$session->get($key_checkin_point);
            } else {
                $arr['task']['checkinPoint'] = $this->check_in_listener->getCheckinPoint( $this->request );
                $session->set($key_checkin_point, $arr['task']['checkinPoint'] );
            }
        } else {
        }
        //cpa
        $key_cpa_visit = $this->keys['cpa_ads'];
        if( $is_alive && $session->has($key_cpa_visit) ) {
            $advertise = $session->get($key_cpa_visit);
        } else {
            $repository = $em->getRepository('JiliApiBundle:Advertiserment');
            $advertise = $repository->getAdvertiserListCPA($id);
            $session->set($key_cpa_visit, $advertise);
        }
        $arr['advertise'] = $advertise;
        $arr['task']['cpa'] = $arr['advertise'];

        return $arr;
    }

    public function reset() {
        $session = $this->session;

        $keys = $this->keys;

        if( isset($keys['alive'])) {
            $session->set($keys['alive'], time());
            unset($keys['alive']);
        }
        foreach(array_keys($keys) as $key ) {
            $session->remove( $key);
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
