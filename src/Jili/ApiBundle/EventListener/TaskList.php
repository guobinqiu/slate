<?php
namespace Jili\ApiBundle\EventListener;

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
    private $keys = array(
        'alive'=>'task_list.alive',
        'game_visit'=>'task_list.game_visit',
        'adv_visit'=>'task_list.adv_visit',
        '91ww_visit'=>'task_list.91ww_visit',
        'checkin_visit'=>'task_list.checkin_visit',
        'checkin_point'=>'task_list.checkin_point',
        'cpa_ads'=>'task_list.cpa_ads',
    );  /* add to config */
    private $duration  = 20;
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
#                $logger->debug('{jarod}'. implode(':', array(__CLASS__,__LINE__,'task list  session alive')));
            } else {
#                $logger->debug('{jarod}'. implode(':', array(__CLASS__,__LINE__,'task list  session out of date')));
                $this->reset();
            }
        } else {
#            $logger->debug('{jarod}'. implode(':', array(__CLASS__,__LINE__,'task list session init')));
            $this->reset();
        }

        //游戏
        $key_game =$this->keys[ 'game_visit'];
        if( $is_alive && $session->has($key_game)) {
            $visit  =  $session->get($key_game);
#            $logger->debug('{jarod}'. implode(':', array(__CLASS__,__LINE__,$key_game,'from sess','') ).var_export($visit, true));
        } else {
            $visit = $em->getRepository('JiliApiBundle:UserGameVisit')->getGameVisit($id, $day);
            $session->set($key_game, $visit);
#            $logger->debug('{jarod}'. implode(':', array(__CLASS__,__LINE__,$key_game,'init sess','') ).var_export($visit, true));
        }

        $arr['task']['game'] =(empty ($visit))? $this->getParameter('init_one'):$arr['task']['game'] = $this->getParameter('init');

        //广告任务墙
        $key_adv = $this->keys['adv_visit'];
        if( $is_alive && $session->has($key_adv)) {
            $visit  =  $session->get($key_adv);
#            $logger->debug('{jarod}'. implode(':', array(__CLASS__,__LINE__,$key_adv,'from sess','') ).var_export($visit, true));
        } else {
            $visit = $em->getRepository('JiliApiBundle:UserAdvertisermentVisit')->getAdvertisermentVisit($id, $day);
            $session->set($key_adv, $visit);
#            $logger->debug('{jarod}'. implode(':', array(__CLASS__,__LINE__,$key_adv,'init sess','') ).var_export($visit, true));
        }

        $arr['task']['ad'] =(empty ($visit))? $this->getParameter('init_one'):$arr['task']['ad'] = $this->getParameter('init');

        //91wenwen
        $key_91ww = $this->keys['91ww_visit'];
        if( $is_alive && $session->has($key_91ww)) {
            $visit  =  $session->get($key_91ww);
#            $logger->debug('{jarod}'. implode(':', array(__CLASS__,__LINE__,$key_91ww,'from sess','') ).var_export($visit, true));
        } else {
            $visit = $em->getRepository('JiliApiBundle:UserWenwenVisit')->getWenwenVisit($id, $day);
            $session->set($key_91ww, $visit);
#            $logger->debug('{jarod}'. implode(':', array(__CLASS__,__LINE__,$key_91ww,'init sess','') ).var_export($visit, true));
        }

        $arr['task']['wen'] =(empty ($visit))? $this->getParameter('init_one'):$arr['task']['wen'] = $this->getParameter('init');

        //签到
        $key_checkin = $this->keys['checkin_visit'];
        if( $is_alive && $session->has($key_checkin)) {
            $visit =  $session->get($key_checkin);
#            $logger->debug('{jarod}'. implode(':', array(__CLASS__,__LINE__,$key_checkin,'from sess','') ).var_export($visit, true));
        } else {
            $visit = $em->getRepository('JiliApiBundle:CheckinClickList')->checkStatus($id, $day);
            $session->set($key_checkin, $visit);
#            $logger->debug('{jarod}'. implode(':', array(__CLASS__,__LINE__,$key_checkin,'init sess','') ).var_export($visit, true));
        }

        $arr['task']['checkin'] = (!empty ($visit)) ? $this->getParameter('init'): $this->getParameter('init_one');

        //获取签到积分
        if( empty ($visit)) {
            $key_checkin_point = $this->keys['checkin_point'];
            if( $is_alive && $session->has($key_checkin_point)) {
                $arr['task']['checkinPoint'] =$session->get($key_checkin_point);
#                $logger->debug('{jarod}'. implode(':', array(__CLASS__,__LINE__,$key_checkin_point,'from sess','') ).var_export($arr['task']['checkinPoint'], true));
            } else {
                $arr['task']['checkinPoint'] = $this->check_in_listener->getCheckinPoint( $this->request );
                $session->set($key_checkin_point, $arr['task']['checkinPoint'] );
#                $logger->debug('{jarod}'. implode(':', array(__CLASS__,__LINE__,$key_checkin_point,'init sess','') ).var_export($arr['task']['checkinPoint'] , true));
            }
        } else {
#            $logger->debug('{jarod}'. implode(':', array(__CLASS__,__LINE__,$key_checkin_point,'not required','') ));
        }
        //cpa
        $key_cpa_visit = $this->keys['cpa_ads'];
        if( $is_alive && $session->has($key_cpa_visit) ) {
            $advertise = $session->get($key_cpa_visit);
#            $logger->debug('{jarod}'. implode(':', array(__CLASS__,__LINE__,$key_cpa_visit,'from sess','') ).var_export($advertise, true));
        } else {
            $repository = $em->getRepository('JiliApiBundle:Advertiserment');
            $advertise = $repository->getAdvertiserListCPA($id);
            $session->set($key_cpa_visit, $advertise);
#            $logger->debug('{jarod}'. implode(':', array(__CLASS__,__LINE__,$key_cpa_visit,'init sess','') ).var_export($advertise, true));
        }
        $arr['advertise'] = $advertise;
        $arr['task']['cpa'] = $arr['advertise'];

        return $arr;
    }

    public function reset() {
        $session = $this->session;
        $session->set($this->keys['alive'], time());
        $session->remove( $this->keys['game_visit']);
        $session->remove( $this->keys['adv_visit']);
        $session->remove( $this->keys['91ww_visit']);
        $session->remove( $this->keys['checkin_visit']);
        $session->remove( $this->keys['checkin_point']);
        $session->remove( $this->keys['cpa_ads']);
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
