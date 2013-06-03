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

class AdvertisermentController extends Controller
{
	/**
	 * @Route("/info/{id}", name="_advertiserment_index")
	 */
	public function infoAction($id)
	{
// 		$uid = 1;
		$uid = $this->get('request')->getSession()->get('uid');
		$em = $this->getDoctrine()->getManager();
		$advertiserment = $em->getRepository('JiliApiBundle:Advertiserment')->find($id);
        $arr['advertiserment'] = $advertiserment;
        $adw_info = $advertiserment->getImageUrl();
        $adw_info = explode("u=",$adw_info);
        $new_url = $adw_info[0]."u=".$uid.$adw_info[1].$id;
        $arr['id'] = $id;
        $arr['adwurl'] = $new_url;
		return $this->render('JiliApiBundle:Advertiserment:info.html.twig',$arr);
	}
	/**
	 * @Route("/list", name="_advertiserment_list")
	 */
	public function listAction(){
		$em = $this->getDoctrine()->getManager();
		$repository = $em->getRepository('JiliApiBundle:Advertiserment');
		$advertise = $repository->getAdvertiserment();
		$adverRecommand = $repository->getAdverRecommandList();
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
		$code = '';
		$request = $this->get('request');
		$id = $request->query->get('id');
		$em = $this->getDoctrine()->getManager();
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
			$adwOrder->setOrderStatus($this->container->getParameter('init'));
			$adwOrder->setDeleteFlag($this->container->getParameter('init'));
			$em->persist($adwOrder);
			$em->flush();
			$code = $this->container->getParameter('init_one');
		}else{
			$issetOrder = $em->getRepository('JiliApiBundle:AdwOrder')->find($order[0]['id']);
			$issetOrder->setOrderStatus($this->container->getParameter('init_one'));
			$em->flush();
			$code = $this->container->getParameter('init_one');
		}
		return new Response($code);
// 		return $this->render('JiliApiBundle:Advertiserment:info.html.twig');
	}
	
	
}
	

	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
