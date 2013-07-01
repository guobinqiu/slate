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
		$em = $this->getDoctrine()->getManager();
		$repository = $em->getRepository('JiliApiBundle:Advertiserment');
		$advertise = $repository->getAdvertiserAreaList($this->container->getParameter('init_five'));
		$adverRecommand = $repository->getAdvertiserAreaList($this->container->getParameter('init_four'));
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
	
	
}
	

	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
