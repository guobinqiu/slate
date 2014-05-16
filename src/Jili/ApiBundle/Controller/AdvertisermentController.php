<?php
namespace Jili\ApiBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Jili\ApiBundle\Entity\AdwAccessHistory;
use Jili\ApiBundle\Entity\AdwOrder;
use Jili\ApiBundle\Entity\Advertiserment;
use Jili\ApiBundle\Entity\TaskHistory00;
use Jili\ApiBundle\Entity\TaskHistory01;
use Jili\ApiBundle\Entity\TaskHistory02;
use Jili\ApiBundle\Entity\TaskHistory03;
use Jili\ApiBundle\Entity\TaskHistory04;
use Jili\ApiBundle\Entity\TaskHistory05;
use Jili\ApiBundle\Entity\TaskHistory06;
use Jili\ApiBundle\Entity\TaskHistory07;
use Jili\ApiBundle\Entity\TaskHistory08;
use Jili\ApiBundle\Entity\TaskHistory09;
use Jili\ApiBundle\Entity\UserAdvertisermentVisit;

class AdvertisermentController extends Controller
{
	/**
	 * @Route("/info/{id}", requirements={"id" = "\d+"},name="_advertiserment_index", requirements={"_scheme"="http"})
	 */
	public function infoAction($id)
	{
		$uid='';
		$reward_multiple = '';
		$uid = $this->get('request')->getSession()->get('uid');
		$campaign_multiple = $this->container->getParameter('campaign_multiple');
		$code = $this->container->getParameter('init');
		$arr['code'] = $code;
        $em = $this->getDoctrine()->getManager();
        if($uid){
            $user = $em->getRepository('JiliApiBundle:User')->find($uid);
            $reward_multiple = $user->getRewardMultiple();
        }
		$arr['uid'] = $uid;
		$arr['orderStatus'] = '';
		$adw = $em->getRepository('JiliApiBundle:AdwOrder');
		$adw_status = $adw->getOrderInfo($uid,$id);
		if($adw_status){
		    $orderStatus = $adw_status[0]['orderStatus'];
            $arr['orderStatus'] = $orderStatus;
        }

		$advertiserment = $em->getRepository('JiliApiBundle:Advertiserment')->find($id);

		if( empty( $advertiserment) ){
			return $this->redirect($this->generateUrl('_default_error'));
        }

		$time =  $advertiserment->getEndTime()->getTimestamp() ;
		if(time() >= $time ) {
			$code = $this->container->getParameter('init_one');
			$arr['code'] = $code;
		}

       if( $advertiserment->getIncentiveType() == 18 ) { // emar
           $image_url = $advertiserment->getImageurl();
           $arr['adwurl'] = str_replace('{member_id}', $uid, $image_url); 
       }  else {
           $adw_info = $advertiserment->getImageurl();
           $adw_info = explode('u=', $adw_info);
           $new_url = trim($adw_info[0]).'u='.$uid.trim($adw_info[1]).$id;
           $arr['adwurl'] = $new_url; 	
       }

        $arr['id'] = $id;

        if(  $advertiserment->getIncentiveType() != 1 ) { // not cpa
            $reward_rate = round( $advertiserment->getIncentiveRate() * $advertiserment->getRewardRate()/10000, 2 ) ;
            $arr['reward_rate'] = $reward_rate;
        }

        $arr['advertiserment'] = $advertiserment;

		return $this->render('JiliApiBundle:Advertiserment:info.html.twig',$arr);
	}

	/**
	 * @Route("/list", name="_advertiserment_list", requirements={"_scheme"="http"})
	 */
	public function listAction(){
        if(!  $this->get('request')->getSession()->get('uid') ) {
            $this->get('request')->getSession()->set( 'referer',  $this->generateUrl('_advertiserment_list') );
            return  $this->redirect($this->generateUrl('_user_login'));
        }

		$em = $this->getDoctrine()->getManager();
		$repository = $em->getRepository('JiliApiBundle:Advertiserment');
		$advertise = $repository->getAdvertiserAreaList($this->container->getParameter('init_three'));
		$adverRecommand = $repository->getAdvertiserAreaList($this->container->getParameter('init_two'));

        $arr['ads'] = array_merge($adverRecommand,$advertise );

        //UserAdvertisermentVisit
        $day = date('Ymd');
        $request = $this->get('request');
        $em = $this->getDoctrine()->getManager();
        $id = $request->getSession()->get('uid');
        $visit = $em->getRepository('JiliApiBundle:UserAdvertisermentVisit')->getAdvertisermentVisit($id, $day);
        if (empty ($visit)) {
            $gameVisit = new UserAdvertisermentVisit();
            $gameVisit->setUserId($id);
            $gameVisit->setVisitDate($day);
            $em->persist($gameVisit);
            $em->flush();
            // remove from session cache.
            $taskList = $this->get('session.task_list');
            $taskList->remove(array( 'adv_visit'));
        }

		return $this->render('JiliApiBundle:Advertiserment:list.html.twig',$arr);
	}

    /**
     * @Route("/offer99", name="_advertiserment_offer99", requirements={"_scheme"="http"})
     */
    public function offer99Action(){
        if(!  $this->get('request')->getSession()->get('uid') ) {
            $this->get('request')->getSession()->set( 'referer',  $this->generateUrl('_advertiserment_offer99') );
            return  $this->redirect($this->generateUrl('_user_login'));
        }

        //UserAdvertisermentVisit
        $day = date('Ymd');
        $request = $this->get('request');
        $em = $this->getDoctrine()->getManager();
        $id = $request->getSession()->get('uid');
        $visit = $em->getRepository('JiliApiBundle:UserAdvertisermentVisit')->getAdvertisermentVisit($id, $day);
        if (empty ($visit)) {
            $gameVisit = new UserAdvertisermentVisit();
            $gameVisit->setUserId($id);
            $gameVisit->setVisitDate($day);
            $em->persist($gameVisit);
            $em->flush();

            // remove from session cache.
            $taskList = $this->get('session.task_list');
            $taskList->remove(array( 'adv_visit'));
        }

        return $this->render('JiliApiBundle:Advertiserment:offer99.html.twig');
    }


	/**
     * @Route("/click", name="_advertiserment_click")
	 */
    public function clickAction(){
        if(!$this->get('request')->getSession()->get('uid')){
            $code = $this->container->getParameter('init');
        }else{
            $request = $this->get('request');
            $id = $request->query->get('id');
            $em = $this->getDoctrine()->getManager();
            $advertiserment = $em->getRepository('JiliApiBundle:Advertiserment')->find($id);

            $service_params = array( 'advertiserment'=> $advertiserment , 'request'=> $request );
            $accessHistory = $this->get('cps_access_history.logger')->log($service_params) ;
            if($advertiserment->getIncentiveType() ==1 ) {
                $point = $this->get('rebate_point.caculator')->calcPointByCategory($advertiserment->getIncentive() , $advertiserment->getIncentiveType() );
            }

            $order = $this->get('cps_order.factory')->get($service_params);
            if(empty($order)){
                $order =  $this->get('cps_order.factory')->init($service_params) ;

                if( $order instanceof AdwOrder &&   $order->getIncentiveType() == 1 ){
                // only adw order cpa has incentive_type field.
                    $params = array(
                        'orderId' => $order->getId(),
                        'userid' => $this->get('request')->getSession()->get('uid'),
                        'taskType' => $this->container->getParameter('init_one'),
                        'categoryType' => $this->container->getParameter('init_one'),
                        'task_name' => $advertiserment->getTitle() ,
                        'point' => $point , 
                        'date' => date_create() ,
                        'status' => $order->getOrderStatus()
                    );
                }else{
                    if($order instanceof \Jili\EmarBundle\Entity\EmarOrder ) {
                        $order_status  = $order->getStatus();
                        $task_type = $this->container->getParameter('emar_com.cps.task_type') ;
                    } else{
                        $order_status  = $order->getOrderStatus();
                        $task_type = $this->container->getParameter('init_one');
                    } 

                    $params = array(
                        'orderId' => $order->getId(),
                        'userid' => $this->get('request')->getSession()->get('uid'),
                        'taskType' => $task_type ,
                        'categoryType' =>$advertiserment->getIncentiveType() ,  #$this->container->getParameter('init_two'),
                        'task_name' => $advertiserment->getTitle() ,
                        'point' => 0,
                        'date' => date_create() ,
                        'status' =>$order_status 
                    );
                }
                $this->getTaskHistory($params);
            } else {
                $service_params['order_id'] =(is_array($order) ) ?  $order[0]['id']: $order->getId() ;
                $order = $this->get('cps_order.factory')->update($service_params);
            }
            $code = $this->container->getParameter('init_one');
        }
        return new Response($code);
    }


	private function getTaskHistory($params=array()){
        return $this->get('general_api.task_history')->init($params);
    }
	
}
	
