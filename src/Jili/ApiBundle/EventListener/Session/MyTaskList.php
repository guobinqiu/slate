<?php
namespace Jili\ApiBundle\EventListener\Session;

use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Doctrine\ORM\EntityManager;

/**
 * 页面上的的的任务列表中数据的生成。 
 **/
class MyTaskList
{

    private $em;
    private $logger;
    private $session;

    private $container;
    private $request;

    private $keys ;//= array(
//        'alive'=>'my_task_list.alive',
//        'list'=>'my_task_list.task_history'
//    );  /* add to config */
    private $duration;//=60;
    public function __construct( $keys, $duration)
    {
        $this->keys = $keys;
        $this->duration = $duration;
    }

    /**
     * @param: $option = array('status' => $type ,'offset'=>'','limit'=>'');
     * @param: $option['status'] 
     * @param: $option['offset']
     * @param: $option['limit']
     */
	public function selTaskHistory($option){

        $this->logger->debug('{jarod}'. implode(':', array(__CLASS__,__LINE__,'')). var_export( $this->keys, true) );
        $this->logger->debug('{jarod}'. implode(':', array(__CLASS__,__LINE__,'')). var_export( $this->duration, true) );
        $session = $this->session;
        $logger = $this->logger;
        $data = array();
        $day=date('Ymd');
        // session life 
        $key_alive = $this->keys['alive'];
        $duration_alive = $this->duration; /* todo: add to config */
        $is_alive = false ;
        if( $session->has($key_alive)) {
            if(  time() < $duration_alive + $session->get($key_alive ) ) {
                $is_alive = true;
            } else {
#                $logger->debug('{jarod}'. implode(':', array(__CLASS__,__LINE__,'task list  session out of date')));
                $this->reset();
            }
        } else {
#            $logger->debug('{jarod}'. implode(':', array(__CLASS__,__LINE__,'task list session init')));
            $this->reset();
        }
        $key_list = $this->keys['list'];

#        $logger->debug('{jarod}'. implode(':', array(__CLASS__,__LINE__,'key:',$key_list)));
#        $logger->debug('{jarod}'. implode(':', array(__CLASS__,__LINE__,'option:')). var_export($option, true));

        if( $is_alive && $session->has($key_list) ) {
            $data = $session->get($key_list);
        } else{
            // 取出全部做写到cache中。
            // todo: 不能总保存在session中!
            $option_all= array( 'status' => 0 );
#            $logger->debug('{jarod}'. implode(':', array(__CLASS__,__LINE__,'option_all:')). var_export($option_all, true));
            $data = $this->selTaskHistoryRaw($option_all);
            $session->set($key_list, $data);
        }

        if(isset($option['status'])) {
            $status = (int) $option['status'];
            if( $status === 1 ) {
                foreach( $data as $key => $value){
                    if(($value['orderStatus'] === 2 && $value['type'] === 1 ) || $value['orderStatus'] === 0 ) {
                        continue;
                    }
                    unset($data[$key]);
                }
            } else if( $status === 2 ) {
                foreach( $data as $key => $value){
                    if( $value['orderStatus'] === 3 || ($value['orderStatus'] === 1 && $value['type'] > 1 )) {
                        continue;
                    }
                    unset($data[$key]);
                }
            } else if( $status === 3 ) {
                foreach( $data as $key => $value){
                    if( $value['orderStatus'] === 4 || ($value['orderStatus'] === 2 && $value['type'] > 1 )) {
                        continue;
                    }
                    unset($data[$key]);
                }
            }  else {
#                $logger->debug('{jarod}'. implode(':', array(__CLASS__,__LINE__,3,'')).var_export( $option['status'], true) );
            }
        }

        // filter by the query??
		if(isset($option['offset']) && $option['offset'] && isset($option['limit']) && $option['limit']){
            $data = array_slice($data, 0, 10);
        }
        return $data; 
    }

	private  function selTaskHistoryRaw($option){
      $userid = $this->session->get('uid');
      $task = $this->em->getRepository('JiliApiBundle:TaskHistory0'. ( $userid % 10) ); 
      $po = $task->getUseradtaste($userid, $option);

      foreach ($po as $key => $value) {
			if($value['type']==1 ) {
				$adUrl = $task->getUserAdwId($value['orderId']);
                if( is_array($adUrl) && count($adUrl) > 0) {
                    $po[$key]['adid'] = $adUrl[0]['adid'];
                } else {
                    $po[$key]['adid'] = '';
                }
			}else{
				$po[$key]['adid'] = '';
			}
		}
		return $po;
    }

    public function reset() {
        $session = $this->session;
        $session->set($this->keys['alive'], time());
        $session->set($this->keys['list'], time());
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

    public function setRequest( $request ) {
        $this->request = $request;
        return $this;
    }

}