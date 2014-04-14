<?php
namespace Jili\ApiBundle\EventListener;

/**
 * 
 **/
class CpsOrderFactory 
{
    private $em;
    private $logger;
    private $container_;

    public function setEntityManager( \Doctrine\ORM\EntityManager $em)  {
        $this->em = $em;
    }

    public function setLogger( \Symfony\Component\HttpKernel\Log\LoggerInterface $logger) {
        $this->logger = $logger;
    }

    /**
     * @params $params  = array( 'request' =>   Symfony\Component\HttpFoundation\Request, 
     *    'advertiserment' => Jili\ApiBundle\Entity\Advertiserment,
     *    'id' => $id );
     * @return $order  
     */
    public function init( $params ) {
        extract($params);

        $em = $this->em;
        $logger = $this->logger;

#         $logger->debug('{jarod}'. implode(',', array(__CLASS__, __LINE__, '') ) );
        $incentive_type = (int) $advertiserment->getIncentiveType();

        if($incentive_type === 2 ||  $incentive_type === 1 ) {
            $order_class = 'Jili\ApiBundle\Entity\AdwOrder';
        } else if ($incentive_type  === $this->getParameter('emar_com.cps.category_type')) {
            $order_class = 'Jili\EmarBundle\Entity\EmarOrder';
        }  else {
            throw new \Exception($order_class. 'not found!');
        } 

        //...
        $order = new $order_class;
        $order->setUserId($request->getSession()->get('uid'));
        $order->setAdId($advertiserment->getId());

         if ($incentive_type  === $this->getParameter('emar_com.cps.category_type') ) {
            $order->setCreatedAt(date_create());
            $order->setAdType('local');
         } else {
            // $order->setCreateTime(date_create());
             $order->setIncentiveType($advertiserment->getIncentiveType() );
         }


        if($incentive_type  ==1){
            #$order->setIncentive($advertiserment[0]['incentive']);
            $order->setIncentive($point);
        } else if($incentive_type ==2){
            $order->setIncentiveRate($advertiserment->getIncentiveRate() );
            $order->setOrderStatus( $this->getParameter('init_one'));
        }

        if ($incentive_type  === $this->getParameter('emar_com.cps.category_type') ) {
            $order->setStatus( $this->getParameter('init_one'));
        }


        if($incentive_type === 2 ||  $incentive_type === 1 ) {
            $order->setCreateTime(date_create(date('Y-m-d H:i:s')));
        } else if ($incentive_type  === $this->getParameter('emar_com.cps.category_type') ) {
            $order->setCreatedAt(date_create(date('Y-m-d H:i:s')));
        }

        $order->setDeleteFlag( $this->getParameter('init') );

        $em->persist($order);
        $em->flush();

        return $order;

    }


    /**
     * @params $params  = array( 'request' =>   Symfony\Component\HttpFoundation\Request, 
     *    'advertiserment' => Jili\ApiBundle\Entity\Advertiserment,
     *    'order_id' => order_id );
     * @return $order  
     */
    public function update( $params ) {
        extract($params);

        $em = $this->em;
        $logger = $this->logger;

        $incentive_type = (int) $advertiserment->getIncentiveType();

        if($incentive_type === 2 ||  $incentive_type === 1 ) {
            $order_repository = 'JiliApiBundle:AdwOrder';
        } else if ($incentive_type  === $this->getParameter('emar_com.cps.category_type') ) {
            $order_repository = 'JiliEmarBundle:EmarOrder';
        }  else {
            $this->logger->crit('{jarod}'. implode(',', array(__FILE__,__LINE__, '') ). var_export(get_class( $advertiserment) , true) );
            throw new \Exception($order_repository. 'not found!');
        } 


        $order = $em->getRepository($order_repository)->findOneById($order_id);
#         $this->logger->debug('{jarod}'. implode(',', array(__FILE__,__LINE__, '') ). var_export($order_id , true) );

#        if($incentive_type === 2 ||  $incentive_type === 1 ) {
#            $order->setCreateTime(date_create(date('Y-m-d H:i:s')));
#        } else if ($incentive_type  === $this->getParameter('emar_com.cps.category_type') ) {
#            #$order->setCreatedAt(date_create(date('Y-m-d H:i:s')));
#        }
#
		$em->flush();
        return $order;
    }

    /**
     *
     */
    public function get($params) {
        extract($params);

        $em = $this->em;
        $logger = $this->logger;
        #$logger->debug('{jarod}'. implode(',', array(__CLASS__, __LINE__, '') ).var_export( $params,true)  );

        $incentive_type = (int) $advertiserment->getIncentiveType();

        if(   $incentive_type === 1 ) {
            // adw cpa 
            $orders = $em->getRepository('JiliApiBundle:AdwOrder')->getOrderInfo($request->getSession()->get('uid'), $advertiserment->getId() );
            if( count($orders) > 0) {
                $order = $orders[0];
            } else {
                $order = null;
            }
        } else if ($incentive_type === 2 ){
           // adw cps 
            $order =  $em->getRepository('JiliApiBundle:AdwOrder')->findOneCpsOrderInit(array(
                'user_id' => $request->getSession()->get('uid'), 
                'ad_id'=> $advertiserment->getId(),
                'status'=> $this->getParameter('init_one'),
                'delete_flag'=> $this->getParameter('init') 
            ) );

        } else if ($incentive_type  === $this->getParameter('emar_com.cps.category_type')  ) {

            //emar cps
            $order =  $em->getRepository('JiliEmarBundle:EmarOrder')->findOneCpsOrderInit(array(
                'user_id' => $request->getSession()->get('uid'), 
                'ad_id'=>  $advertiserment->getId(),
                'ad_type'=>  'local',
                'status'=> $this->getParameter('init_one'),
                'delete_flag'=> $this->getParameter('init') 
            ) );
        }  else {
#             $this->logger->debug('{jarod}'. implode(',', array(__FILE__,__LINE__, '') ). var_export(get_class( $advertiserment) , true) );
            throw new \Exception(' incentive type of '. $incentive_type. 'not support!');
        } 
        return $order;
    }

    public function setContainer( $c) {
        $this->container_ = $c;
    }

    public function getParameter($key) {
        return $this->container_->getParameter($key);
    }
}
