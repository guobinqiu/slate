<?php
namespace Jili\ApiBundle\Controller;
use Jili\ApiBundle\Entity\AdwApiReturn;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Jili\ApiBundle\Entity\AdwAccessHistory;
use Jili\ApiBundle\Entity\PointHistory00;


class ApiController extends Controller
{
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
		$comm = $request->query->get('comm');
		$type = $request->query->get('type');
		$u_sig = md5("date=".$request->query->get('date')."&time=".$request->query->get('time')."&promotionID=".$request->query->get('promotionID')."&comm=".$request->query->get('comm')."&totalPrice=".$request->query->get('totalPrice')."&ocd=".$request->query->get('ocd')."&XLGt8P9wgCz9QPfJ");
		$order = $em->getRepository('JiliApiBundle:AdwOrder')->getOrderInfo($uid,$adid);
		if($order){
			$adver = $em->getRepository('JiliApiBundle:Advertiserment')->find($adid);
			$issetStauts = $em->getRepository('JiliApiBundle:AdwOrder')->getOrderInfo($uid,$adid,$this->container->getParameter('init_two'));
			if($issetStauts){
				$code = 5;
			}else{
				$issetOrder = $em->getRepository('JiliApiBundle:AdwOrder')->find($order[0]['id']);
				$issetOrder->setComm($comm);
				if($type==2){
					$issetOrder->setIncentiveRate(intval($comm*30));
				}
				$issetOrder->setOrderStatus($this->container->getParameter('init_two'));
				$issetOrder->setAdwReturnTime(date_create(date('Y-m-d H:i:s')));
				$em->flush();
				$code = 1;
			}
		}else{
			$code = 2;
		}
		return new Response($code);
	}

}
