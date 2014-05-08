<?php
namespace Jili\EmarBundle\EventListener;

use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Doctrine\ORM\EntityManager;

/**
 *  emar_counter
 **/
class EmarRequestCounter
{
    private $logger;
    private $em;

    private $starttime;/*每次请求发起的时间*/
    private $endtime;/*每次请求结束的时间*/

    private $mode = 0; /*工作模式: 0: closed ，1:open for debug */


    public function increase( $tag ) {

        if( $this->mode === 0 ) {
            return false;
        }
        if( empty($tag)) {
            $tag  = date('YmdHi');
        }

        $totaltime = $this->endtime - $this->starttime;

        $em = $this->em;
        $row = $em->getRepository('JiliEmarBundle:EmarRequest')->findOneByTag($tag);

        if( ! $row) {
            $row = new \Jili\EmarBundle\Entity\EmarRequest;
            $row->setCount(1);
            $row->setTag($tag);

            if( $totaltime > 0) {
                $row->setTimeConsumedTotal($totaltime);
            }

            $em->persist($row);
        } else {
            $row->setCount( $row->getCount()  + 1);

            if( $totaltime > 0) {
                $row->setTimeConsumedTotal($row->getTimeConsumedTotal() + $totaltime);
            }
        }
        $em->flush();
    } 

    public function start(){
        if( $this->mode === 0 ) {
            return false;
        }
        $this->starttime = $this->getMicrtime();
    }
    public function complete(){

        if( $this->mode === 0 ) {
            return false;
        }
        $this->endtime = $this->getMicrtime();
    }

    private function getMicrtime(){
        $mtime = microtime(); 
        $mtime = explode(" ",$mtime); 
        return  $mtime[1] + $mtime[0]; 
    }

    public function setEntityManager(  EntityManager $em) {
        $this->em= $em;
    }

    public function setLogger(  LoggerInterface $logger) {
        $this->logger = $logger;
    }

    /**
     * @param: $mode 表示是否打开计数功能： 0表示关闭， 1或其它数字表示打开。
     **/
    public function setMode( $mode) {
        if( is_numeric($mode)  ) {
            $this->mode = $mode ;
        } else {
            $this->mode = 0;
        }
    }

}

