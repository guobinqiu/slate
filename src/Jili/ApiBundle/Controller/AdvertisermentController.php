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
	 * @Route("/info/{id}", requirements={"id" = "\d+"},name="_advertiserment_index")
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
		if($advertiserment){
			$advertiserment = $em->getRepository('JiliApiBundle:Advertiserment')->getAdwAdverList($advertiserment->getIncentiveType(),$id);
			if($reward_multiple){
				if($advertiserment[0]['incentiveType']==2){
                    $cps_rate = $reward_multiple > $campaign_multiple ? $reward_multiple : $campaign_multiple;
                    $advertiserment[0]['reward_rate'] = $advertiserment[0]['incentiveRate'] * $advertiserment[0]['rewardRate'] * $cps_rate;
                    $advertiserment[0]['reward_rate']= round($advertiserment[0]['reward_rate']/10000,2);
                }
			}else{
				if($advertiserment[0]['incentiveType']==2){
                    $advertiserment[0]['reward_rate'] = $advertiserment[0]['incentiveRate'] * $advertiserment[0]['rewardRate'] * $campaign_multiple;
                    $advertiserment[0]['reward_rate']= round($advertiserment[0]['reward_rate']/10000,2);
                }
			}
		}else
			return $this->redirect($this->generateUrl('_default_error'));
		$time =  $advertiserment[0]['endTime']->format('Y-m-d H:i:s');
		if(time()-strtotime($time)>=0){
			$code = $this->container->getParameter('init_one');
			$arr['code'] = $code;
		}
        $adw_info = $advertiserment[0]['imageurl'];
        $adw_info = explode("u=",$adw_info);
        $new_url = trim($adw_info[0])."u=".$uid.trim($adw_info[1]).$id;
        $arr['id'] = $id;
        $arr['adwurl'] = $new_url; 	
        $arr['advertiserment'] = $advertiserment[0];
		return $this->render('JiliApiBundle:Advertiserment:info.html.twig',$arr);
	}

	/**
	 * @Route("/list", name="_advertiserment_list")
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

        #$logger= $this->get('logger');
        #$logger->debug('{jaord}'.__FILE__.'@'.__LINE__.':'. var_export( count( $arr['ads']), true));

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
        }

		return $this->render('JiliApiBundle:Advertiserment:list.html.twig',$arr);
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
		    $advertiserment = $em->getRepository('JiliApiBundle:Advertiserment')->getAdwAdverList($advertiserment->getIncentiveType(),$id);
		    
			$adwAccessHistory = new AdwAccessHistory();
			$adwAccessHistory->setUserId($this->get('request')->getSession()->get('uid'));
			$adwAccessHistory->setAdId($id);
			$adwAccessHistory->setAccessTime(date_create(date('Y-m-d H:i:s')));
			$em->persist($adwAccessHistory);
			$em->flush();
			$order = $em->getRepository('JiliApiBundle:AdwOrder')->getOrderInfo($this->get('request')->getSession()->get('uid'),$id);

            if($advertiserment[0]['incentiveType']==1){
                $point = $this->get('rebate_point.caculator')->calcPointByCategory($incentive, $advertiserment[0]['incentiveType']);
            }

			if(empty($order)){

				$adwOrder = new AdwOrder();
				$adwOrder->setUserId($this->get('request')->getSession()->get('uid'));
				$adwOrder->setAdId($id);
				$adwOrder->setCreateTime(date_create(date('Y-m-d H:i:s')));
				$adwOrder->setIncentiveType($advertiserment[0]['incentiveType']);

				if($advertiserment[0]['incentiveType']==1){
					#$adwOrder->setIncentive($advertiserment[0]['incentive']);
					$adwOrder->setIncentive($point);
				} else if($advertiserment[0]['incentiveType']==2){
					$adwOrder->setIncentiveRate($advertiserment[0]['incentiveRate']);
				}

				$adwOrder->setOrderStatus($this->container->getParameter('init_one'));
				$adwOrder->setDeleteFlag($this->container->getParameter('init'));
				$em->persist($adwOrder);
				$em->flush();

                if($adwOrder->getIncentiveType()==1){
                	$parms = array(
	                  'orderId' => $adwOrder->getId(),
	                  'userid' => $this->get('request')->getSession()->get('uid'),
	                  'task_type' => $this->container->getParameter('init_one'),
	                  'categoryId' => $this->container->getParameter('init_one'),
	                  'taskName' => $advertiserment[0]['title'],
	                  'point' => $point , 
	                  'date' => date('Y-m-d H:i:s'),
	                  'status' => $adwOrder->getOrderStatus()
	                );
                }else{
                	$parms = array(
	                  'orderId' => $adwOrder->getId(),
	                  'userid' => $this->get('request')->getSession()->get('uid'),
	                  'task_type' => $this->container->getParameter('init_one'),
	                  'categoryId' => $this->container->getParameter('init_two'),
	                  'taskName' => $advertiserment[0]['title'],
	                  'point' => 0,
	                  'date' => date('Y-m-d H:i:s'),
	                  'status' => $adwOrder->getOrderStatus()
	                );

                }
                $this->getTaskHistory($parms);

			}else{
				$issetOrder = $em->getRepository('JiliApiBundle:AdwOrder')->find($order[0]['id']);
				$issetOrder->setCreateTime(date_create(date('Y-m-d H:i:s')));
				$em->flush();
			}
			$code = $this->container->getParameter('init_one');
		}
		return new Response($code);
	}

	private function getTaskHistory($parms=array()){
	  extract($parms);
      if(strlen($userid)>1){
            $uid = substr($userid,-1,1);
      }else{
            $uid = $userid;
      }
      switch($uid){
            case 0:
                  $po = new TaskHistory00();
                  break;
            case 1:
                  $po = new TaskHistory01();
                  break;
            case 2:
                  $po = new TaskHistory02();
                  break;
            case 3:
                  $po = new TaskHistory03();
                  break;
            case 4:
                  $po = new TaskHistory04();
                  break;
            case 5:
                  $po = new TaskHistory05();
                  break;
            case 6:
                  $po = new TaskHistory06();
                  break;
            case 7:
                  $po = new TaskHistory07();
                  break;
            case 8:
                  $po = new TaskHistory08();
                  break;
            case 9:
                  $po = new TaskHistory09();
                  break;
      }


      $em = $this->getDoctrine()->getManager();
      $po->setOrderId($orderId);
      $po->setUserId($userid);
      $po->setTaskType($task_type);
      $po->setCategoryType($categoryId);
      $po->setTaskName($taskName);
      $po->setPoint($point);
      $po->setDate(date_create($date));
      $po->setStatus($status);
      $em->persist($po);
      $em->flush();
    }

	
	
}
	

	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
