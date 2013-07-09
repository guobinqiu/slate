<?php
namespace Jili\ApiBundle\Controller;
use Jili\ApiBundle\Repository\AdwOrderRepository;

use Jili\ApiBundle\Entity\AdwApiReturn;
use Jili\ApiBundle\Entity\AdwOrder;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Jili\ApiBundle\Entity\AdwAccessHistory;
use Jili\ApiBundle\Entity\PointHistory00;


class ApiController extends Controller
{
	private function getTime($date,$time){
		$arrayDate[] = substr($date,0,4);
		$arrayDate[] = substr($date,4,2);
		$arrayDate[] = substr($date,6,2);
		$arrayTime[] = substr($time,0,2);
		$arrayTime[] = substr($time,2,2);
		$arrayTime[] = substr($time,4,2);
		$join[] = implode("/",$arrayDate);
		$join[] = implode(":",$arrayTime);
		return implode(" ",$join);
	}
	/**
	 * @Route("/getAdwInfo", name="_api_getAdwInfo")
	 */
	public function getAdwInfoAction()
	{
		$em = $this->getDoctrine()->getManager();
		$request = $this->get('request');
		$adwapi = new AdwApiReturn();
		$adwapi->setContent($request->getRequestUri());
		$em->persist($adwapi);
		$em->flush();
		$code = array('code'=>'','msg'=>'');
		$uid = $request->query->get('userinfo');
		$adid = $request->query->get('extinfo');
		$date = $request->query->get('date');
		$time = $request->query->get('time');
		$happenTime = $this->getTime($date,$time);
		$comm = $request->query->get('comm');
		$type = $request->query->get('type');
		$ocd = $request->query->get('ocd');
		$totalPrice = $request->query->get('totalPrice');
// 		$u_sig = md5("date=".$request->query->get('date')."&time=".$request->query->get('time')."&promotionID=".$request->query->get('promotionID')."&comm=".$request->query->get('comm')."&totalPrice=".$request->query->get('totalPrice')."&ocd=".$request->query->get('ocd')."&XLGt8P9wgCz9QPfJ");
		$order = $em->getRepository('JiliApiBundle:AdwOrder')->getOrderInfo($uid,$adid);
		if($order){
            if($type==1){
            	$issetStauts = $em->getRepository('JiliApiBundle:AdwOrder')->getOrderInfo($uid,$adid,$happenTime,$this->container->getParameter('init_two'));
            	if($issetStauts){
            		$code = 5;
            	}else{
            		$issetOrder = $em->getRepository('JiliApiBundle:AdwOrder')->find($order[0]['id']);
            		$issetOrder->setComm($comm);
            		$issetOrder->setHappenTime(date_create($happenTime));
            		$issetOrder->setOrderStatus($this->container->getParameter('init_two'));
            		$issetOrder->setAdwReturnTime(date_create(date('Y-m-d H:i:s')));
            		$em->flush();
            		$code = 1;
            	}
            }else{//cps
            	$issetCpsInfo = $em->getRepository('JiliApiBundle:AdwOrder')->getCpsInfo($uid,$adid);
            	if($issetCpsInfo[0]['ocd']){
            		$cpsOrder = new AdwOrder();
            		$cpsOrder->setUserId($uid);
            		$cpsOrder->setAdId($adid);
            		$cpsOrder->setCreateTime(date_create(date('Y-m-d H:i:s')));
            		$cpsOrder->setHappenTime(date_create($happenTime));
            		$cpsOrder->setAdwReturnTime(date_create(date('Y-m-d H:i:s')));
            		$cpsOrder->setIncentiveType($type);
            		$cpsOrder->setIncentive(intval($comm*30));
            		$cpsOrder->setOcd($ocd);
            		$cpsOrder->setComm($comm);
            		$cpsOrder->setOrderPrice($totalPrice);
            		$cpsOrder->setOrderStatus($this->container->getParameter('init_two'));
            		$cpsOrder->setDeleteFlag($this->container->getParameter('init'));
            		$em->persist($cpsOrder);
            		$em->flush();
            	}else{
            		$cpsOrder = $em->getRepository('JiliApiBundle:AdwOrder')->find($issetCpsInfo[0]['id']);
            		$cpsOrder->setComm($comm);
            		$cpsOrder->setOcd($ocd);
            		$cpsOrder->setOrderPrice($totalPrice);
            		$cpsOrder->setIncentive(intval($comm*30));
            		$cpsOrder->setHappenTime(date_create($happenTime));
            		$cpsOrder->setOrderStatus($this->container->getParameter('init_two'));
            		$cpsOrder->setAdwReturnTime(date_create(date('Y-m-d H:i:s')));
            		$em->flush();
            	}
            	$code = 1;
            }
		}else{
			$code = 2;
		}
		return new Response($code);
	}

}
