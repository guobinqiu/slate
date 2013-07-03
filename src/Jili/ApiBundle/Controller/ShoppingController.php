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
		$uid = '';
		$uid = $this->get('request')->getSession()->get('uid');
		if($this->get('request')->getSession()->get('uid'))
			$uid = $this->get('request')->getSession()->get('uid');
		$em = $this->getDoctrine()->getManager();
		$repository = $em->getRepository('JiliApiBundle:Advertiserment');
		$advertise = $repository->getAdvertiserAreaList($this->container->getParameter('init_five'));
		$adverRecommand = $repository->getAdvertiserAreaList($this->container->getParameter('init_four'));
		foreach ( $advertise as $k=>$v){
			$adnum = $em->getRepository('JiliApiBundle:AdwOrder')->getOrderNum($v['id']);
			$advertise[$k]['num'] = $adnum;
// 			if($uid){
// 				$adw_info = $v['imageurl'];
// 				$adw_info = explode("u=",$adw_info);
// 				$new_url = $adw_info[0]."u=".$uid.$adw_info[1].$v['id'];
// 				$advertise[$k]['imageurl'] = $new_url;
// 			}
		}
		$arr['adverRecommand'] = $adverRecommand;
		$arr['advertiserment'] = $advertise;
		$paginator  = $this->get('knp_paginator');
		$arr['pagination'] = $paginator->paginate(
				$advertise,
				$this->get('request')->query->get('page', 1),
				$this->container->getParameter('page_num')
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
		$uid = $this->get('request')->getSession()->get('uid');
		if($this->get('request')->getSession()->get('uid')){
			$uid = $this->get('request')->getSession()->get('uid');
			$em = $this->getDoctrine()->getManager();
			$advertiserment = $em->getRepository('JiliApiBundle:Advertiserment')->find($aid);
			$adw_info = $advertiserment->getImageurl();
			$adw_info = explode("u=",$adw_info);
			$new_url = $adw_info[0]."u=".$uid.$adw_info[1].$aid;
		}
		return new Response($new_url);
	}
	
}
	

	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
