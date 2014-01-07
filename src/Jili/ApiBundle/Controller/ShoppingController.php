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
	 * @Route("/list/{id}", requirements={"id" = "\d+"},name="_shopping_list")
	 */
	public function listAction($id=0)
	{
		$reward_multiple = '';
		$str = '';
		$uid = '';
		$uid = $this->get('request')->getSession()->get('uid');
		$campaign_multiple = $this->container->getParameter('campaign_multiple');
		$em = $this->getDoctrine()->getManager();
		$actCate = $em->getRepository('JiliApiBundle:ActivityCategory')->findAll();
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
                    $advertise[$key]['reward_rate'] = round($advertise[$key]['reward_rate']/10000,2);
                }
            }else{
                if($value['incentiveType']==2){
                    $advertise[$key]['reward_rate'] = $value['incentiveRate'] * $value['rewardRate'] * $campaign_multiple;
                    $advertise[$key]['reward_rate'] = round($advertise[$key]['reward_rate']/10000,2);
                }
            }
		}
		foreach ( $adverRecommand as $key=>$value){
            if($reward_multiple){
                if($value['incentiveType']==2){
                    $cps_rate = $reward_multiple > $campaign_multiple ? $reward_multiple : $campaign_multiple;
                    $adverRecommand[$key]['reward_rate'] = $value['incentiveRate'] * $value['rewardRate'] * $cps_rate;
                    $adverRecommand[$key]['reward_rate'] = round($adverRecommand[$key]['reward_rate']/10000,2);
                }
            }else{
                if($value['incentiveType']==2){
                    $adverRecommand[$key]['reward_rate'] = $value['incentiveRate'] * $value['rewardRate'] * $campaign_multiple;
                    $adverRecommand[$key]['reward_rate'] = round($adverRecommand[$key]['reward_rate']/10000,2);

                }
            }
		}
		$arr['adverRecommand'] = $adverRecommand;
		
		$nowCate = $em->getRepository('JiliApiBundle:Advertiserment')->getAdvCate();
		foreach ($nowCate as $key => $value) {
        	$arrType[] = $value['type'];
        }
        foreach ($arrType as  $value) {
        	$str .= $value.',';
        }
        $allCate = explode(",",$str);
        if($id){
        	if(!in_array($id,$allCate)){
        		return $this->redirect($this->generateUrl('_default_error'));
        	}
			foreach ($advertise as $key => $value) {
				$cate = explode(",",$value['cate']);
				if(!in_array($id,$cate)){
					unset($advertise[$key]);
				}
			}
		}
        foreach ($actCate as $key => $value) {
    		if(!in_array($value->getId(),$allCate)){
    			unset($actCate[$key]);
    		}
    	}
    	$arr['cate'] = $actCate;
		$arr['advertiserment'] = $advertise;
		$paginator  = $this->get('knp_paginator');
		$arr['pagination'] = $paginator->paginate(
				$advertise,
				$this->get('request')->query->get('page', 1),
				25
		);
		$arr['pagination']->setTemplate('JiliApiBundle::pagination.html.twig');
		$arr['uid'] = $uid;
		$arr['id'] = $id;
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
	

	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	