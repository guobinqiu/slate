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

class AdvertisermentController extends Controller
{
	/**
	 * @Route("/info/{id}", name="_advertiserment_index")
	 */
	public function infoAction($id)
	{
		$code = $this->container->getParameter('init');
		$arr['code'] = $code;
		$uid='';
		$uid = $this->get('request')->getSession()->get('uid');
		if($this->get('request')->getSession()->get('uid')){
			$uid = $this->get('request')->getSession()->get('uid');
		}
		$arr['uid'] = $uid;
		$em = $this->getDoctrine()->getManager();
		
		$arr['orderStatus'] = '';
		$adw = $em->getRepository('JiliApiBundle:AdwOrder');
		$adw_status = $adw->getOrderInfo($uid,$id);
		if($adw_status){
		    $orderStatus = $adw_status[0]['orderStatus'];
            $arr['orderStatus'] = $orderStatus;
		}
		$advertiserment = $em->getRepository('JiliApiBundle:Advertiserment')->find($id);
		if($advertiserment)
			$advertiserment = $em->getRepository('JiliApiBundle:Advertiserment')->getAdwAdverList($advertiserment->getIncentiveType(),$id);
		else
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
		$em = $this->getDoctrine()->getManager();
		$repository = $em->getRepository('JiliApiBundle:Advertiserment');
		$advertise = $repository->getAdvertiserAreaList($this->container->getParameter('init_three'));
		$adverRecommand = $repository->getAdvertiserAreaList($this->container->getParameter('init_two'));
		$arr['adverRecommand'] = $adverRecommand;
		$arr['advertiserment'] = $advertise;
		$paginator  = $this->get('knp_paginator');
		$arr['pagination'] = $paginator->paginate(
				$advertise,
				$this->get('request')->query->get('page', 1),
				 $this->container->getParameter('page_num')
		);
		$arr['pagination']->setTemplate('JiliApiBundle::pagination.html.twig');
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
			if(empty($order)){
				$adwOrder = new AdwOrder();
				$adwOrder->setUserId($this->get('request')->getSession()->get('uid'));
				$adwOrder->setAdId($id);
				$adwOrder->setCreateTime(date_create(date('Y-m-d H:i:s')));
				$adwOrder->setIncentiveType($advertiserment[0]['incentiveType']);
				if($advertiserment[0]['incentiveType']==1){
					$adwOrder->setIncentive($advertiserment[0]['incentive']);
				}
				if($advertiserment[0]['incentiveType']==2){
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
	                  'point' => $advertiserment[0]['incentive'],
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
	

	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
