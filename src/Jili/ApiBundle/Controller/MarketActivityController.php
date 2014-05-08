<?php
namespace Jili\ApiBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Jili\ApiBundle\Entity\AdwOrder;
use Jili\ApiBundle\Entity\Advertiserment;
use Jili\ApiBundle\Entity\ActivityMall;
use Jili\ApiBundle\Entity\ActivityCategory;
use Jili\ApiBundle\Entity\MarketActivity;

/**
 * @Route(requirements={"_scheme"="http"})
 */
class MarketActivityController extends Controller
{
	/**
	 * @Route("/index/{aid}/{cateId}",requirements={"aid" = "\d+","cateId" = "\d+"},name="_marketActivity_index")
	 */
	public function indexAction($aid = 0,$cateId = 0)
	{
		$arr = array();
		$newArr = array();
		$str = '';
		$em = $this->getDoctrine()->getManager();
		$actCate = $em->getRepository('JiliApiBundle:ActivityCategory')->findAll();
		$busiAct = $em->getRepository('JiliApiBundle:MarketActivity')->nowActivity($aid);
		if(empty($busiAct)){
			return $this->redirect($this->generateUrl('_default_error'));
		}
        $nowMall = $em->getRepository('JiliApiBundle:MarketActivity')->nowMall();
        $nowCate = $em->getRepository('JiliApiBundle:MarketActivity')->nowCate();
        foreach ($nowCate as $key => $value) {
        	$arr[] = $value['categoryId'];
        }
        foreach ($arr as  $value) {
        	$str .= $value.',';
        }
		$allCate = explode(",",$str);//所有广告分类
        //去除没有广告的分类
		foreach ($actCate as $key => $value) {
    		if(!in_array($value->getId(),$allCate)){
    			unset($actCate[$key]);
    		}
    	}
		if($cateId){
			if(!in_array($cateId,$allCate)){
        		return $this->redirect($this->generateUrl('_default_error'));
        	}
			foreach ($busiAct as $key => $value) {
				$cate = explode(",",$value['categoryId']);
				if(!in_array($cateId,$cate)){
					unset($busiAct[$key]);
				}
			}
		}
		$paginator  = $this->get('knp_paginator');
        $pagin = $paginator->paginate(
        		$busiAct,
        		$this->get('request')->query->get('page', 1),
        		$this->container->getParameter('page_num')
        );
        $pagin->setTemplate('JiliApiBundle::pagination.html.twig');
		return $this->render('JiliApiBundle:MarketActivity:index.html.twig',
					array('nowMall'=>$nowMall,
						  'cate'=>$actCate,
						  'busi'=>$busiAct,
						  'aid'=>$aid,
						  'cateId'=>$cateId,
						  'pagin'=>$pagin
						  ));
	}

	/**
	 * @Route("/info/{id}",name="_marketActivity_info")
	 */
	public function infoAction($id)
	{
		$em = $this->getDoctrine()->getManager();
		$uid = $this->get('request')->getSession()->get('uid');
		$busiAct = $em->getRepository('JiliApiBundle:MarketActivity')->existMarket($id);
		if(empty($busiAct)){
			return $this->redirect($this->generateUrl('_default_error'));
		}
		$adver = $em->getRepository('JiliApiBundle:Advertiserment')->find($busiAct[0]['aid']);
		$adw_info = explode("u=",$adver->getImageurl());
        $new_url = trim($adw_info[0])."u=".$uid.trim($adw_info[1]).$id;
		$yixun = $busiAct[0]['activityUrl'];
		return $this->render('JiliApiBundle:MarketActivity:info.html.twig',
				array('url'=>$new_url,'yixun'=>$yixun));

	}

	
	
}
	

	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
