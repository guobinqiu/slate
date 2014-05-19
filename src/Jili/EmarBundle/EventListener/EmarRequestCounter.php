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

#    private $starttime;/*每次请求发起的时间*/
#    private $endtime;/*每次请求结束的时间*/

    private $size; /* 请求返回的数据大小 Unit: */
    private $mode = 0; /*工作模式: 0: closed ，1:open for debug */

    /**
     * @param: $tag time tag 'YmdHi'
     * @param: $curl_info 
     **/
    public function increase( $tag , $curl_info =array()) {

        if( $this->mode === 0 ) {
            return false;
        }

        if( empty($tag)) {
            $tag  = date('YmdHi');
        }


        $size_down = 0; // 下行: 进来91jili的
        if(! empty($curl_info)) {
            $size_down += ( isset( $curl_info['header_size']) ) ? $curl_info['header_size']: 0;
            $size_down += ( isset( $curl_info['size_download']) ) ? $curl_info['size_download']: 0;
            $size_down += ( isset( $curl_info['download_content_length']) && $curl_info['download_content_length'] > 0 ) ? $curl_info['size_download']: 0;
        }

        $size_up = 0; // 上行: 出去91jili的
        if(! empty($curl_info)) {
            $size_up += ( isset( $curl_info['request_size']) ) ? $curl_info['request_size']: 0;
            $size_up += ( isset( $curl_info['size_upload']) ) ? $curl_info['size_upload']: 0;
            $size_up += ( isset( $curl_info['upload_content_length']) ) ? $curl_info['upload_content_length']: 0;
        }

#        $totaltime = $this->endtime - $this->starttime;
        $totaltime = 0;
        if(! empty($curl_info)) {
            $totaltime += $curl_info['total_time'];
        }

        $em = $this->em;
        $row = $em->getRepository('JiliEmarBundle:EmarRequest')->findOneByTag($tag);

        if( ! $row) {
            $row = new \Jili\EmarBundle\Entity\EmarRequest;
            $row->setCount(1);
            $row->setTag($tag);

            if( $totaltime > 0) {
                $row->setTimeConsumedTotal($totaltime);
            }
            $row->setSizeUp($size_up);
            $row->setSizeDown($size_down);
            

            $em->persist($row);
        } else {
            $row->setCount( $row->getCount()  + 1);

            if( $totaltime > 0) {
                $row->setTimeConsumedTotal($row->getTimeConsumedTotal() + $totaltime);
            }
            if( $size_up > 0) {
                $row->setSizeUp($row->getSizeUp() + (int) $size_up);
            }
            if( $size_down > 0) {
                $row->setSizeDown($row->getSizeDown() + (int) $size_down);
            }
        }
        $em->flush();
    } 

#    public function start(){
#        if( $this->mode === 0 ) {
#            return false;
#        }
#        $this->starttime = $this->getMicrtime();
#    }
#    public function complete(){
#
#        if( $this->mode === 0 ) {
#            return false;
#        }
#        $this->endtime = $this->getMicrtime();
#    }
#
#    private function getMicrtime(){
#        $mtime = microtime(); 
#        $mtime = explode(" ",$mtime); 
#        return  $mtime[1] + $mtime[0]; 
#    }

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

    public function getMode() {
        return $this->mode;
    }

}

