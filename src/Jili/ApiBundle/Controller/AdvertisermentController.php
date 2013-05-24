<?php
namespace Jili\ApiBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Jili\ApiBundle\Entity\AdwAccessHistory;
use Jili\ApiBundle\Entity\Advertiserment;

class AdvertisermentController extends Controller
{
	/**
	 * @Route("/info/{id}", name="_advertiserment_index")
	 */
	public function infoAction($id)
	{
		$em = $this->getDoctrine()->getManager();
		$advertiserment = $em->getRepository('JiliApiBundle:Advertiserment')->find($id);
        $arr['advertiserment'] = $advertiserment;
		return $this->render('JiliApiBundle:Advertiserment:info.html.twig',$arr);
	}
	/**
	 * @Route("/list", name="_advertiserment_list")
	 */
	public function listAction(){
		$em = $this->getDoctrine()->getManager();
		$repository = $em->getRepository('JiliApiBundle:Advertiserment');
		$advertise = $repository->getAdvertiserment();

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
	

	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
