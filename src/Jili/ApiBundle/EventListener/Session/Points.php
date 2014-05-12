<?php
namespace Jili\ApiBundle\EventListener\Session;

use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Doctrine\ORM\EntityManager;
/**
 * 
 **/
class Points
{
    
    private $session;
    private $em;
    private $logger;

    private $keys;
    private $duration;
    public function __construct( $keys, $duration)
    {
        $this->keys = $keys;
        $this->duration = $duration;
    }
    public function getConfirm()
    {
        $session = $this->session;

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

        $key_points_confirming = $this->keys['confirmming'];

        if($is_alive &&  $session->has($key_points_confirming)) {
            $confirmPoints=$session->get($key_points_confirming);
        } else {
            $id = $session->get('uid');
            $task =  $this->em->getRepository('JiliApiBundle:TaskHistory0'. ( $id % 10 ) );
            $confirmPoints = $task->getConfirmPoints($id);
            if(!$confirmPoints){
                $confirmPoints = 0;
            }
            $session->set($key_points_confirming,$confirmPoints);
        }

        return $confirmPoints;
    }

    public function reset() {
        $session = $this->session;
        $session->set($this->keys['alive'], time());
        $session->remove( $this->keys['confirmming']);
    }

    public function setLogger(LoggerInterface $logger) {
        $this->logger = $logger;
        return $this;
    }

    public function setSession( $session){
        $this->session = $session;
        return $this;
    }
    public function setEntityManager( EntityManager $em){
        $this->em = $em;
        return $this;
    }
}


