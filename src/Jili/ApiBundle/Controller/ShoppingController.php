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

class ShoppingController extends Controller
{
	/**
	 * @Route("/list", name="_shopping_list")
	 */
	public function listAction()
	{
		$reward_multiple = '';
		$uid = '';
		$uid = $this->get('request')->getSession()->get('uid');
		$campaign_multiple = $this->container->getParameter('campaign_multiple');
		$em = $this->getDoctrine()->getManager();
		if($uid){
			$user = $em->getRepository('JiliApiBundle:User')->find($uid);
        	$reward_multiple = $user->getRewardMultiple();
		}
		$repository = $em->getRepository('JiliApiBundle:Advertiserment');
		$advertise = $repository->getAdvertiserAreaList($this->container->getParameter('init_five'));
		$adverRecommand = $repository->getAdvertiserAreaList($this->container->getParameter('init_four'));
		foreach ( $advertise as $key=>$value){
			$adnum = $em->getRepository('JiliApiBundle:AdwOrder')->getOrderNum($value['id']);
			$advertise[$key]['num'] = $adnum;
            if($reward_multiple){
                if($value['incentiveType']==2){
                    $cps_rate = $reward_multiple > $campaign_multiple ? $reward_multiple : $campaign_multiple;
                    $advertise[$key]['reward_rate'] = $value['incentiveRate'] * $value['rewardRate'] * $cps_rate;
                }
            }else{
                if($value['incentiveType']==2){
                    $advertise[$key]['reward_rate'] = $value['incentiveRate'] * $value['rewardRate'] * $campaign_multiple;
                }
            }
		}
		foreach ( $adverRecommand as $key=>$value){
            if($reward_multiple){
                if($value['incentiveType']==2){
                    $cps_rate = $reward_multiple > $campaign_multiple ? $reward_multiple : $campaign_multiple;
                    $adverRecommand[$key]['reward_rate'] = $value['incentiveRate'] * $value['rewardRate'] * $cps_rate;
                }
            }else{
                if($value['incentiveType']==2){
                    $adverRecommand[$key]['reward_rate'] = $value['incentiveRate'] * $value['rewardRate'] * $campaign_multiple;
                }
            }
		}
		$arr['adverRecommand'] = $adverRecommand;
		$arr['advertiserment'] = $advertise;
		$paginator  = $this->get('knp_paginator');
		$arr['pagination'] = $paginator->paginate(
				$advertise,
				$this->get('request')->query->get('page', 1),
				25
		);
		$arr['pagination']->setTemplate('JiliApiBundle::pagination.html.twig');
		$arr['uid'] = $uid;
		return $this->render('JiliApiBundle:Advertiserment:shoppinglist.html.twig',$arr);
		
	}
	
	/**
	 * @Route("/info", name="_shopping_info")
	 */
	public function infoAction()
	{
		$new_url = '';
		$request = $this->get('request');
		$aid = $request->query->get('aid');
		$uid = $request->getSession()->get('uid');
		if($uid){
			$em = $this->getDoctrine()->getManager();
			$advertiserment = $em->getRepository('JiliApiBundle:Advertiserment')->find($aid);
			$adw_info = $advertiserment->getImageurl();
			$adw_info = explode("u=",$adw_info);
			$new_url = $adw_info[0]."u=".$uid.$adw_info[1].$aid;
		}
		return new Response($new_url);
	}
	
}
	

	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
