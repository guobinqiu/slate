<?php
namespace Jili\ApiBundle\Controller;

use Jili\ApiBundle\Repository\AdPositionRepository;
use Jili\ApiBundle\Entity\RateAdResult;
use Jili\ApiBundle\Entity\LimitAdResult;
use Jili\ApiBundle\Form\EditBannerType;
use Jili\ApiBundle\Form\AddAdverType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Cookie;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Jili\ApiBundle\Entity\Advertiserment;
use Jili\ApiBundle\Entity\AdPosition;
use Jili\ApiBundle\Entity\AdBanner;
use Jili\ApiBundle\Entity\CallBoard;
use Jili\ApiBundle\Entity\CbCategory;
use Jili\ApiBundle\Entity\LimitAd;
use Jili\ApiBundle\Entity\RateAd;
use Jili\ApiBundle\Entity\TaskOrder;
use Jili\ApiBundle\Entity\AdwOrder;
use Jili\ApiBundle\Entity\PointHistory00;
use Jili\ApiBundle\Entity\PointHistory01;
use Jili\ApiBundle\Entity\PointHistory02;
use Jili\ApiBundle\Entity\PointHistory03;
use Jili\ApiBundle\Entity\PointHistory04;
use Jili\ApiBundle\Entity\PointHistory05;
use Jili\ApiBundle\Entity\PointHistory06;
use Jili\ApiBundle\Entity\PointHistory07;
use Jili\ApiBundle\Entity\PointHistory08;
use Jili\ApiBundle\Entity\PointHistory09;


class AdminController extends Controller
{
    private function getAdminIp(){
        if($_SERVER['REMOTE_ADDR'] == $this->container->getParameter('admin_ele_ip') || 
            $_SERVER['REMOTE_ADDR'] == $this->container->getParameter('admin_un_ip') ||
            $_SERVER['REMOTE_ADDR'] == $this->container->getParameter('admin_vpn_ip'))
            return false;
        else
            return true;
          
    }
	/**
	 * @Route("/login", name="_admin_login")
	 */
    public function loginAction()
    {
        if($this->getAdminIp())
              return $this->redirect($this->generateUrl('_default_error'));
        $code = $this->container->getParameter('init');
        $request = $this->get('request');
        $username = $request->request->get('username');
        $password = $request->request->get('password');
        if ($request->getMethod() == 'POST') {
            if($username=='admin' && $password=='admin'){
                // $session = new Session();
                // $session->start();
                // $session->set('admin_name', $username);
                $code = $this->container->getParameter('init');
                return $this->redirect($this->generateUrl('_admin_index' ));
            }else{
                $code = $this->container->getParameter('init_one');
            }
        }
        return $this->render('JiliApiBundle:Admin:login.html.twig',array('code'=>$code));

      
    }

    /**
     * @Route("/gameAd", name="_admin_gameAd")
     */
    public function GameAdAction(){
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
        return $this->render('JiliApiBundle:Admin:gameAd.html.twig');
    }
    
    /**
     * @Route("/game", name="_admin_game")
     */
    public function GameAction(){
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
        return $this->render('JiliApiBundle:Admin:game.html.twig');
    }

    /**
     * @Route("/adwAd", name="_admin_adwAd")
     */
    public function AdwAdAction(){
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
        return $this->render('JiliApiBundle:Admin:adwAd.html.twig');
    }
    
    //没有通过认证
    private function noCertified($userId,$adid,$happentime){
    	$em = $this->getDoctrine()->getManager();
    	$adworder = $em->getRepository('JiliApiBundle:AdwOrder')->getOrderInfo($userId,$adid,$happentime);
    	if(empty($adworder)){
    		return false;
    	}else{
    		$adworder = $em->getRepository('JiliApiBundle:AdwOrder')->find($adworder[0]['id']);
    		$adworder->setConfirmTime(date_create(date('Y-m-d H:i:s')));
    		$adworder->setOrderStatus($this->container->getParameter('init_four'));
    		$em->persist($adworder);
    		$em->flush();
            $parms = array(
              'userid' => $userId,
              'orderId' => $adworder->getId(),
              'taskType' => $this->container->getParameter('init_one'),
              'point' => $adworder->getIncentive(),
              'date' => date('Y-m-d H:i:s'),
              'status' => $this->container->getParameter('init_four')
            );
            $this->updateTaskHistory($parms);  
    		return true;

    	}
    }
    //已经认证
    private function hasCertified($userId,$adid,$happentime,$comm){
    	$em = $this->getDoctrine()->getManager();
        $advertiserment = $em->getRepository('JiliApiBundle:Advertiserment')->find($adid);
        if($advertiserment->getIncentiveType()==2){
            $rewardRate = $advertiserment->getRewardRate();
            $users = $em->getRepository('JiliApiBundle:User')->find($userId);
            $user_rate = $users->getRewardMultiple();
            $campaign_multiple = $this->container->getParameter('campaign_multiple');
            $rate = $user_rate > $campaign_multiple ? $user_rate : $campaign_multiple;
            $cps_reward = intval($comm*$rewardRate*$rate);
        }
    	$adworder = $em->getRepository('JiliApiBundle:AdwOrder')->getOrderInfo($userId,$adid,$happentime);
    	if(empty($adworder)){
    		return false;
    	}else{
    		$adworder = $em->getRepository('JiliApiBundle:AdwOrder')->find($adworder[0]['id']);
            if($adworder->getIncentiveType()==2){
                $adworder->setIncentive(intval($cps_reward));
            }
    		$adworder->setConfirmTime(date_create(date('Y-m-d H:i:s')));
    		$adworder->setOrderStatus($this->container->getParameter('init_three'));
    		$em->persist($adworder);
    		$em->flush();
            $parms = array(
              'userid' => $userId,
              'orderId' => $adworder->getId(),
              'taskType' => $this->container->getParameter('init_one'),
              'point' => $adworder->getIncentive(),
              'date' => date('Y-m-d H:i:s'),
              'status' => $this->container->getParameter('init_three')
            );
            $this->updateTaskHistory($parms);  
    		if($adworder->getIncentiveType()==1){
    			$limitAd = $em->getRepository('JiliApiBundle:LimitAd')->findByAdId($adid);
    			$limitrs = new LimitAdResult();
    			$limitrs->setAccessHistoryId($adworder->getId());
    			$limitrs->setUserId($userId);
    			$limitrs->setLimitAdId($limitAd[0]->getId());
    			$limitrs->setResultIncentive($adworder->getIncentive());
    			$em->persist($limitrs);
    			$em->flush();
                $this->getPointHistory($userId,$adworder->getIncentive());
    			$user = $em->getRepository('JiliApiBundle:User')->find($userId);
    			$user->setPoints(intval($user->getPoints()+$adworder->getIncentive()));
    			$em->persist($user);
    			$em->flush();
		
    		}else{
    			$rateAd = $em->getRepository('JiliApiBundle:RateAd')->findByAdId($adid);
    			$raters = new RateAdResult();
    			$raters->setAccessHistoryId($adworder->getId());
    			$raters->setUserId($userId);
    			$raters->setRateAdId($rateAd[0]->getId());
    			$raters->setResultPrice($adworder->getComm());
    			$raters->setResultIncentive($adworder->getIncentive());
    			$em->persist($raters);
    			$em->flush();
                $this->getPointHistory($userId,$adworder->getIncentive());
    			$user = $em->getRepository('JiliApiBundle:User')->find($userId);
    			$user->setPoints(intval($user->getPoints()+$raters->getResultIncentive()));
    			$em->persist($user);
    			$em->flush();
    			
    		}
    		return true;
    	}
    	
    }

    private function updateTaskHistory($parms=array()){
      extract($parms);
      if(strlen($userid)>1){
            $uid = substr($userid,-1,1);
      }else{
            $uid = $userid;
      }
      $em = $this->getDoctrine()->getManager();
      switch($uid){
            case 0:
                  $task = $em->getRepository('JiliApiBundle:TaskHistory00'); 
                  break;
            case 1:
                  $task = $em->getRepository('JiliApiBundle:TaskHistory01');  
                  break;
            case 2:
                  $task = $em->getRepository('JiliApiBundle:TaskHistory02');  
                  break;
            case 3:
                  $task = $em->getRepository('JiliApiBundle:TaskHistory03'); 
                  break;
            case 4:
                  $task = $em->getRepository('JiliApiBundle:TaskHistory04'); 
                  break;
            case 5:
                  $task = $em->getRepository('JiliApiBundle:TaskHistory05'); 
                  break;
            case 6:
                  $task = $em->getRepository('JiliApiBundle:TaskHistory06'); 
                  break;
            case 7:
                  $task = $em->getRepository('JiliApiBundle:TaskHistory07'); 
                  break;
            case 8:
                  $task = $em->getRepository('JiliApiBundle:TaskHistory08'); 
                  break;
            case 9:
                  $task = $em->getRepository('JiliApiBundle:TaskHistory09'); 
                  break;
      }
      $task_order = $task->getFindOrderId($orderId,$taskType);
      $po = $task->findById($task_order[0]['id']);
      $po[0]->setDate(date_create($date));
      $po[0]->setPoint($point);
      $po[0]->setStatus($status);
      $em->persist($po[0]);
      $em->flush();
    }
    
    private function getPointHistory($userid,$point){
    	if(strlen($userid)>1){
    		$uid = substr($userid,-1,1);
    	}else{
    		$uid = $userid;
    	}
    	switch($uid){
    	    case 0:
    	    	$po = new PointHistory00();
    	    	break;
	    	case 1:
	    		$po = new PointHistory01();
	    		break;
		    case 2:
		    	$po = new PointHistory02();
			    break;
			case 3:
				$po = new PointHistory03();
				break;
			case 4:
				$po = new PointHistory04();
				break;
			case 5:
				$po = new PointHistory05();
				break;
			case 6:
				$po = new PointHistory06();
				break;
			case 7:
				$po = new PointHistory07();
				break;
			case 8:
				$po = new PointHistory08();
				break;
			case 9:
				$po = new PointHistory09();
				break;
    	}
		$em = $this->getDoctrine()->getManager();
		$po->setUserId($userid);
		$po->setPointChangeNum($point);
		$po->setReason($this->container->getParameter('init_one'));
		$em->persist($po);
		$em->flush();
    }
    
    private function getStatus($uid,$aid,$happentime){
    	$em = $this->getDoctrine()->getManager();
    	$adwStatus = $em->getRepository('JiliApiBundle:AdwOrder')->getOrderStatus($uid,$aid,$happentime);
    	if(empty($adwStatus))
    		return true;
    	else
    		return false;
    }


     /**
     * @Route("/importCpsAdver", name="_admin_importCpsAdver")
     */
    public function importCpsAdverAction()
    {
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
        $code = array();
        $request = $this->get('request');
        $success = '';
        $userId = '';
        $adid = '';
        if ($request->getMethod('post') == 'POST') {
            $success = $this->container->getParameter('init_one');
            if (isset($_FILES['csv'])) {
                $file = $_FILES['csv']['tmp_name']; 
                $handle = fopen($file,'r'); 
                while ($data = fgetcsv($handle)){ 
                   $goods_list[] = $data;
                   unset($goods_list[0]);
                }
                foreach ($goods_list as $k=>$v){
                    $status = iconv('gb2312','UTF-8//IGNORE',$v[6]);
                    $name = iconv('gb2312','UTF-8//IGNORE',$v[0]);
                    $adid = explode("'",$v[7]);
                    $userId = explode("'",$v[8]);
                    if($this->getStatus($userId[1],$adid[1],$v[2])){
                        if($status == $this->container->getParameter('nothrough')){
                            if(!$this->noCertified($userId[1],$adid[1],$v[2])){
                                $code[] = $name.'-'.$userId[1].'-'.$adid[1].'插入数据失败';
                            }
                        }
                        if($status == $this->container->getParameter('certified')){
                            if(!$this->hasCertified($userId[1],$adid[1],$v[2],$v[5])){
                                $code[] =  $name.'-'.$userId[1].'-'.$adid[1].'插入数据失败';
                            }
                        }
                    }
                    
                }
                fclose($handle);
            }
        }
        $arr['success'] = $success;
        $arr['code'] = $code;
        return $this->render('JiliApiBundle:Admin:importCpsAdver.html.twig',$arr);

    }
    
    
    /**
     * @Route("/importAdver", name="_admin_importAdver")
     */
    public function importAdverAction()
    {
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
    	$code = array();
    	$request = $this->get('request');
    	$success = '';
    	$userId = '';
    	$adid = '';
    	if ($request->getMethod('post') == 'POST') {
    		$success = $this->container->getParameter('init_one');
    	    if (isset($_FILES['csv'])) {
                $file = $_FILES['csv']['tmp_name']; 
                $handle = fopen($file,'r'); 
                while ($data = fgetcsv($handle)){ 
                   $goods_list[] = $data;
                   unset($goods_list[0]);
                }
                foreach ($goods_list as $k=>$v){
                	$status = iconv('gb2312','UTF-8//IGNORE',$v[5]);
                	$name = iconv('gb2312','UTF-8//IGNORE',$v[0]);
                	$adid = explode("'",$v[7]);
                	$userId = explode("'",$v[8]);
                	if($this->getStatus($userId[1],$adid[1],$v[2])){
                		if($status == $this->container->getParameter('nothrough')){
                			if(!$this->noCertified($userId[1],$adid[1],$v[2])){
                				$code[] = $name.'-'.$userId[1].'-'.$adid[1].'插入数据失败';
                			}
                		}
                		if($status == $this->container->getParameter('certified')){
                			if(!$this->hasCertified($userId[1],$adid[1],$v[2],$v[4])){
                				$code[] =  $name.'-'.$userId[1].'-'.$adid[1].'插入数据失败';
                			}
                		}
                	}
                	
                }
                fclose($handle);
    	    }
    	}
    	$arr['success'] = $success;
    	$arr['code'] = $code;
    	return $this->render('JiliApiBundle:Admin:importAdver.html.twig',$arr);
    }
    
    /**
     * @Route("/delBanner/{id}", name="_admin_delBanner")
     */
    public function delBannerAction($id)
    {
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
    	$em = $this->getDoctrine()->getManager();
    	$banner = $em->getRepository('JiliApiBundle:AdBanner')->find($id);
    	$em->remove($banner);
    	$em->flush();
    	return $this->redirect($this->generateUrl('_admin_infoBanner'));
    }
    

    /**
     * @Route("/addBanner", name="_admin_addBanner")
     */
    public function addBannerAction()
    {
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
        $code ='';
        $codeflag = '';
        $banner = new adBanner();
        $form  = $this->createForm(new EditBannerType(),$banner);
        $request = $this->get('request');
        $bannerUrl = $request->request->get('url');
        $position = $request->request->get('position');
        $em = $this->getDoctrine()->getManager();
        $bannerNum = $em->getRepository('JiliApiBundle:AdBanner')->findAll();
        if ($request->getMethod() == 'POST') {
            if($bannerUrl && $position){
                $isPostion = $em->getRepository('JiliApiBundle:AdBanner')->findByPosition($position);
                if($isPostion){
                    $codeflag = $this->container->getParameter('init_two');
                }else{
                    $form->bind($request);
                    $path =  $this->container->getParameter('upload_banner_dir');
                    $banner->setAdUrl($bannerUrl);
                    $banner->setPosition($position);
                    $banner->setCreateTime(date_create(date('Y-m-d H:i:s')));
                    $em->persist($banner);
                    $code = $banner->upload($path);
                    if(!$code){
                        $em->flush();
                        return $this->redirect($this->generateUrl('_admin_infoBanner'));
                    }
                }
                
            }else{
                $codeflag = $this->container->getParameter('init_one');

            }
            
        }
        return $this->render('JiliApiBundle:Admin:addBanner.html.twig',
                array('url'=>$bannerUrl,'position'=>$position,'form' => $form->createView(),'code'=>$code,'codeflag'=>$codeflag));
    }
    
    /**
     * @Route("/infoBanner", name="_admin_infoBanner")
     */
    public function infoBannerAction()
    {
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
    	$request = $this->get('request');
    	$em = $this->getDoctrine()->getManager();
    	$adbanner = $em->getRepository('JiliApiBundle:AdBanner')->getInfoAdminBanner();
    	$paginator = $this->get('knp_paginator');
    	$arr['pagination'] = $paginator
    	->paginate($adbanner,
    			$request->query->get('page', 1), $this->container->getParameter('page_num'));
    	$arr['pagination']->setTemplate('JiliApiBundle::pagination.html.twig');
    	return $this->render('JiliApiBundle:Admin:infoBanner.html.twig',$arr);
    
    }
    
    /**
     * @Route("/editBanner/{id}", name="_admin_editBanner")
     */
    public function editBannerAction($id)
    {
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
    	$code ='';
        $codeflag = '';
    	$request = $this->get('request');
    	$url = $request->request->get('url');
        $position = $request->request->get('position');
    	$em = $this->getDoctrine()->getManager();
    	$adbanner = $em->getRepository('JiliApiBundle:AdBanner')->find($id);
    	$form  = $this->createForm(new EditBannerType(),$adbanner);
    	if ($request->getMethod() == 'POST') {
             if($url && $position){
                $form->bind($request);
                $path =  $this->container->getParameter('upload_banner_dir');
                $isPostion = $em->getRepository('JiliApiBundle:AdBanner')->getBannerPosition($position,$id);
                if($isPostion){
                    $findBanner = $em->getRepository('JiliApiBundle:AdBanner')->find($isPostion[0]['id']);
                    $findBanner->setPosition($adbanner->getPosition());
                    $findBanner->setCreateTime(date_create(date('Y-m-d H:i:s')));
                    $em->persist($adbanner);
                    $em->flush();
                }
                $adbanner->setAdUrl($url);
                $adbanner->setPosition($position);
                $adbanner->setCreateTime(date_create(date('Y-m-d H:i:s')));
                $em->persist($adbanner);
                $code = $adbanner->editupload($path);
                if(!$code){
                    $em->flush();
                    return $this->redirect($this->generateUrl('_admin_infoBanner'));
                }
             }else{
                $codeflag = $this->container->getParameter('init_one');
             }
            
    	}
    	return $this->render('JiliApiBundle:Admin:editBanner.html.twig',
    			array('banner'=>$adbanner,'form' => $form->createView(),'code'=>$code,'codeflag'=>$codeflag));
    
    }
    
    
    /**
     * @Route("/addPostion", name="_admin_addPostion")
     */
    public function addPostionAction()
    {
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
    	$advermentTitle = '';
    	$adposition = '';
    	$code = '';
    	$ad_title = '';
    	$postion = new AdPosition();
    	$codeflag = $this->container->getParameter('init');
    	$request = $this->get('request');
    	$position = $request->request->get('position');
    	$title = $request->request->get('title');
    	$adid = $request->query->get('id');
    	$number = $request->request->get('number');
    	$em = $this->getDoctrine()->getManager();
    	if($adid){
        	$ad_adv = $em->getRepository('JiliApiBundle:Advertiserment')->find($adid);
        	$advermentTitle = $ad_adv->getTitle();
    	}
    	if ($request->getMethod() == 'POST') {
    		if($request->request->get('search')){
    			$adposition = $em->getRepository('JiliApiBundle:Advertiserment')->getSearchAd($title);
    			if(!empty($adposition)){
    				$code = $this->container->getParameter('init_one');
    			}else{
    				$codeflag = $this->container->getParameter('init_five');
    			}
    		}
    		if($request->request->get('selected')){
    			$ck = $request->request->get('ck');
    			if($ck){
    				$ad_position = $em->getRepository('JiliApiBundle:Advertiserment')->find($ck);
    				$ad_title = $ad_position->getTitle();
    			}else{
    				$adposition = $em->getRepository('JiliApiBundle:Advertiserment')->getSearchAd($title);
    				$codeflag = $this->container->getParameter('init_four');
    			}
    		}
    		if($request->request->get('add')){
    			if($title && $position && $number){
    				if($adid){
    					$exist = $em->getRepository('JiliApiBundle:AdPosition')->getAdPosition($position);
    					foreach($exist as $k=>$v){
    						$existNum[] = $v['position'];
    					}
    					if(in_array($number,$existNum)){
    						$codeflag = $this->container->getParameter('init_three');
    					}else{
    						$postion->setType($position);
    						$postion->setPosition($number);
    						$postion->setAdId($adid);
    						$em->persist($postion);
    						$em->flush();
    						return $this->redirect($this->generateUrl('_admin_infoPostion'));
    					}
    					
    				}else{
    					$adverment = $em->getRepository('JiliApiBundle:Advertiserment')->findByTitle($title);
    					if(empty($adverment)){
    						$codeflag = $this->container->getParameter('init_two');
    					}else{
    						$exist = $em->getRepository('JiliApiBundle:AdPosition')->getAdPosition($position);
    						foreach($exist as $k=>$v){
    							$existNum[] = $v['position'];
    						}
    						if(in_array($number,$existNum)){
    							$codeflag = $this->container->getParameter('init_three');
    						}else{
    							$postion->setType($position);
    							$postion->setPosition($number);
    							$postion->setAdId($adverment[0]->getId());
    							$em->persist($postion);
    							$em->flush();
    							return $this->redirect($this->generateUrl('_admin_infoPostion'));
    						}
    					}
    				}
    				
    			}else{
    				$codeflag = $this->container->getParameter('init_one');
    			}
    		}
    	}
    	return $this->render('JiliApiBundle:Admin:addPostion.html.twig',
    			array('codeflag'=>$codeflag,'adid'=>$adid,'code'=>$code,
    				  'title'=>$title,'ad_title'=>$ad_title,'position'=>$position,'number'=>$number,
    					'adposition'=>$adposition,'advermentTitle'=>$advermentTitle));
    }
    
    /**
     * @Route("/searchPosition", name="_admin_searchPosition")
     */
    public function searchPositionAction()
    {
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
    	$ad_code = '';
    	$request = $this->get('request');
    	$title = $request->request->get('title');
    	$em = $this->getDoctrine()->getManager();
    	if ($request->getMethod() == 'POST') {
    		$adposition = $em->getRepository('JiliApiBundle:Advertiserment')->getSearchAd($title);
    		if(empty($adposition)){
    			$ad_code = $this->container->getParameter('init_one');
    		}else{
    			$ad_code = $this->container->getParameter('init_two');
    		}
    	}
    	return $this->render('JiliApiBundle:Admin:searchPosition.html.twig',array('adposition'=>$adposition,'ad_code'=>$ad_code));
    	
    }
    
    
    /**
     * @Route("/delAdPosition/{id}", name="_admin_delAdPosition")
     */
    public function delAdPositionAction($id)
    {
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
    	$em = $this->getDoctrine()->getManager();
    	$adposition = $em->getRepository('JiliApiBundle:AdPosition')->find($id);
    	$adposition->setPosition($this->container->getParameter('init'));
    	$em->persist($adposition);
    	$em->flush();
    	return $this->redirect($this->generateUrl('_admin_infoPostion'));
    }
    
    /**
     * @Route("/editPostion/{id}", name="_admin_editPostion")
     */
    public function editPostionAction($id)
    {
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
    	$codeflag = $this->container->getParameter('init');
    	$request = $this->get('request');
    	$em = $this->getDoctrine()->getManager();
    	$adver = $em->getRepository('JiliApiBundle:AdPosition')->getInfoPosition($id);
    	$position = $request->request->get('position');
    	$number = $request->request->get('number');
    	if ($request->getMethod() == 'POST') {
    		if($position && $number){
    			$exist = $em->getRepository('JiliApiBundle:AdPosition')->getAdPosition($position);
    			foreach($exist as $k=>$v){
    				$existNum[] = $v['position'];
    			}
    			if(in_array($number,$existNum)){
    				$codeflag = $this->container->getParameter('init_two');
    			}else{
    				$adposition = $em->getRepository('JiliApiBundle:AdPosition')->find($id);
    				$adposition->setType($position);
    				$adposition->setPosition($number);
    				$em->persist($adposition);
    				$em->flush();
    				return $this->redirect($this->generateUrl('_admin_infoPostion'));
    			}
    		}else{
    			$codeflag = $this->container->getParameter('init_one');
    		}
    	}
    	return $this->render('JiliApiBundle:Admin:editPostion.html.twig',array('adver'=>$adver[0],'codeflag'=>$codeflag));
    }
    
    /**
     * @Route("/infoPostion", name="_admin_infoPostion")
     */
    public function infoPostionAction()
    {
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
    	$request = $this->get('request');
    	$em = $this->getDoctrine()->getManager();
    	$adver = $em->getRepository('JiliApiBundle:Advertiserment')->getAdvertiserment();
    	$paginator = $this->get('knp_paginator');
    	$arr['pagination'] = $paginator
    	->paginate($adver,
    			$request->query->get('page', 1), $this->container->getParameter('page_num'));
    	$arr['pagination']->setTemplate('JiliApiBundle::pagination.html.twig');
    	return $this->render('JiliApiBundle:Admin:infoPostion.html.twig',$arr);
    	
    	
    }
    
    
    /**
     * @Route("/addAdver", name="_admin_addAdver")
     */
    public function addAdverAction()
    {
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
    	$code = '';
    	$codeflag = $this->container->getParameter('init');
    	$adver = new Advertiserment();
    	$em = $this->getDoctrine()->getManager();
    	$request = $this->get('request');
    	$title = $request->request->get('title');
    	$start_time = $request->request->get('start_time');
    	$end_time = $request->request->get('end_time');
    	$info = $request->request->get('info');
    	$comment = $request->request->get('comment');
    	$url = $request->request->get('url');
    	$category = $request->request->get('category');
    	$score = $request->request->get('score');
        $intReward = $request->request->get('intReward');
        $rewardRate = $request->request->get('rewardRate');
    	$rule = $request->request->get('rule');
    	$form  = $this->createForm(new AddAdverType(),$adver);
    	if ($request->getMethod() == 'POST') {
    		if($title && $start_time && $end_time && $info && $comment && $rule && $url && $category){ 
                if(($category == 1 && $score) || ($category == 2 && $intReward && $rewardRate)){
                    $form->bind($request);
                    $path =  $this->container->getParameter('upload_adver_dir');
                    $adver->setType($this->container->getParameter('init'));
                    $adver->setTitle($title);
                    $adver->setStartTime(date_create($start_time));
                    $adver->setEndTime(date_create($end_time));
                    $adver->setDecription($comment);
                    $adver->setImageurl($url);
                    $adver->setIncentiveType($category);
                    $adver->setCategory($category);
                    if($category==1){
                        $adver->setIncentive($score);
                    }else{
                        $adver->setIncentiveRate($intReward);
                        $adver->setRewardRate($rewardRate);
                    }
                    $adver->setContent($info);
                    $adver->setInfo($rule);
                    $adver->setDeleteFlag($this->container->getParameter('init'));
                    $em->persist($adver);
                    $code = $adver->upload($path);
                    if(!$code){
                        $em->flush();
                        if($adver->getIncentiveType()==1){
                            $limit = new LimitAd();
                            $limit->setAdId($adver->getId());
                            $limit->setIncentive($adver->getIncentive());
                            $limit->setIncome(floor($adver->getIncentive()/30));
                            $em->persist($limit);
                            $em->flush();
                        }else{
                            $rate = new RateAd();
                            $rate->setAdId($adver->getId());
                            $rate->setIncentiveRate($adver->getIncentiveRate());
                            $em->persist($rate);
                            $em->flush();
                            
                        }
                        return $this->redirect($this->generateUrl('_admin_infoAdver'));
                    }

                }else{
                    $codeflag = $this->container->getParameter('init_one');
                }
    		}else{
    			$codeflag = $this->container->getParameter('init_one');
    		}
    	}
    	return $this->render('JiliApiBundle:Admin:addAdver.html.twig',
            			    array(
            					'form' => $form->createView(),
            					'code'=>$code,
            					'codeflag'=>$codeflag,
            			    	'title'=>$title,
            			    	'start_time'=>$start_time,
            			    	'end_time'=>$end_time,
            			    	'info'=>$info,
            			    	'comment'=>	$comment,
            			    	'url'=>	$url,
            			    	'category'=>$category,
            			    	'score'=>$score,
            			    	'rule'=>$rule
            			    		
            					));
    }
    
    /**
     * @Route("/infoAdver", name="_admin_infoAdver")
     */
    public function infoAdverAction()
    {
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
    	$request = $this->get('request');
    	$em = $this->getDoctrine()->getManager();
    	$adver = $em->getRepository('JiliApiBundle:Advertiserment')->getAllAdvertiserList();
//     	$time =  $adver[0]['endTime']->format('Y-m-d H:i:s');
    	$paginator = $this->get('knp_paginator');
    	$arr['pagination'] = $paginator
    	->paginate($adver,
    			$request->query->get('page', 1), $this->container->getParameter('page_num'));
    	$arr['pagination']->setTemplate('JiliApiBundle::pagination.html.twig');
    	return $this->render('JiliApiBundle:Admin:infoAdver.html.twig',$arr);
    	 
    }
    
    /**
     * @Route("/stopAdver/{id}", name="_admin_stopAdver")
     */
    public function stopAdverAction($id)
    {
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
    	$em = $this->getDoctrine()->getManager();
    	$adver = $em->getRepository('JiliApiBundle:Advertiserment')->find($id);
    	$stopTime = date("Y-m-d",strtotime("-1 day"));
    	$adver->setEndTime(date_create($stopTime));
    	$em->persist($adver);
    	$em->flush();
    	return $this->redirect($this->generateUrl('_admin_infoAdver'));
    }
    
    /**
     * @Route("/delAdver/{id}", name="_admin_delAdver")
     */
    public function delAdverAction($id)
    {
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
    	$em = $this->getDoctrine()->getManager();
    	$adver = $em->getRepository('JiliApiBundle:Advertiserment')->find($id);
    	$adver->setDeleteFlag($this->container->getParameter('init_one'));
    	$em->persist($adver);
    	$em->flush();
    	return $this->redirect($this->generateUrl('_admin_infoAdver'));
    }
    
    /**
     * @Route("/editAdver/{id}", name="_admin_editAdver")
     */
    public function editAdverAction($id)
    {
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
    	$code = '';
    	$codeflag = $this->container->getParameter('init');
    	$em = $this->getDoctrine()->getManager();
    	$adver = $em->getRepository('JiliApiBundle:Advertiserment')->find($id);
    	$request = $this->get('request');
    	$title = $request->request->get('title');
    	$start_time = $request->request->get('start_time');
    	$end_time = $request->request->get('end_time');
    	$info = $request->request->get('info');
    	$comment = $request->request->get('comment');
    	$url = $request->request->get('url');
    	$category = $request->request->get('category');
    	$score = $request->request->get('score');
        $intReward = $request->request->get('intReward');
        $rewardRate = $request->request->get('rewardRate');
    	$rule = $request->request->get('rule');
    	$form  = $this->createForm(new AddAdverType(),$adver);
    	if ($request->getMethod() == 'POST') {
    		if($title && $start_time && $end_time && $info && $comment && $rule && $url && $category){
                if(($category == 1 && $score) || ($category == 2 && $intReward && $rewardRate)){
                    $form->bind($request);
                    $path =  $this->container->getParameter('upload_adver_dir');
                    $adver->setType($this->container->getParameter('init'));
                    $adver->setTitle($title);
                    $adver->setStartTime(date_create($start_time));
                    $adver->setEndTime(date_create($end_time));
                    $adver->setDecription($comment);
                    $adver->setImageurl($url);
                    $adver->setIncentiveType($category);
                    $adver->setCategory($category);
                    if($category==1){
                        $adver->setIncentive($score);
                    }else{
                        $adver->setIncentiveRate($intReward);
                        $adver->setRewardRate($rewardRate);
                    }
                    $adver->setContent($info);
                    $adver->setInfo($rule);
                    $adver->setDeleteFlag($this->container->getParameter('init'));
                    $em->persist($adver);
                    $code = $adver->upload($path);
                    if(!$code || $code=='图片为必填项'){
                        $em->flush();
                        if($adver->getIncentiveType()==1){
                            $limit = $em->getRepository('JiliApiBundle:LimitAd')->findByAdId($adver->getId());
                            if(empty($limit)){
                                $del = $em->getRepository('JiliApiBundle:RateAd')->findByAdId($adver->getId());
                                $em->remove($del[0]);
                                $em->flush();
                                $new_limit = new LimitAd();
                                $new_limit->setAdId($adver->getId());
                                $new_limit->setIncentive($adver->getIncentive());
                                $new_limit->setIncome(floor($adver->getIncentive()/30));
                                $em->persist($new_limit);
                                $em->flush();
                            }else{
                                $limit[0]->setAdId($adver->getId());
                                $limit[0]->setIncentive($adver->getIncentive());
                                $limit[0]->setIncome(floor($adver->getIncentive()/30));
                                $em->persist($limit[0]);
                                $em->flush();
                            }
                        }else{
                            $rate = $em->getRepository('JiliApiBundle:RateAd')->findByAdId($adver->getId());
                            if(empty($rate)){
                                $del = $em->getRepository('JiliApiBundle:LimitAd')->findByAdId($adver->getId());
                                $em->remove($del[0]);
                                $em->flush();
                                $new_rate = new RateAd();
                                $new_rate->setAdId($adver->getId());
                                $new_rate->setIncentiveRate($adver->getIncentiveRate());
                                $em->persist($new_rate);
                                $em->flush();
                            }else{
                                $rate[0]->setAdId($adver->getId());
                                $rate[0]->setIncentiveRate($adver->getIncentiveRate());
                                $em->persist($rate[0]);
                                $em->flush();
                            }
                            
                        }
                        return $this->redirect($this->generateUrl('_admin_infoAdver'));
                    }
                }else{
                    $codeflag = $this->container->getParameter('init_one');
                }
    		}else{
    			$codeflag = $this->container->getParameter('init_one');
    		}
    	}
    	return $this->render('JiliApiBundle:Admin:editAdver.html.twig',
			    array(
					'adver'=>$adver,
					'form' => $form->createView(),
		    		'code'=>$code,
		    		'codeflag'=>$codeflag,
		    		'title'=>$title,
		    		'start_time'=>$start_time,
		    		'end_time'=>$end_time,
		    		'info'=>$info,
		    		'comment'=>	$comment,
		    		'url'=>	$url,
		    		'category'=>$category,
		    		'score'=>$score,
		    		'rule'=>$rule
					));
    
    }
    
    /**
     * @Route("/editCallboard/{id}", name="_admin_editCallboard")
     */
    public function editCallboardAction($id)
    {
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
    	$codeflag = $this->container->getParameter('init');
    	$request = $this->get('request');
    	$em = $this->getDoctrine()->getManager();
        $cb_category = $em->getRepository('JiliApiBundle:CbCategory')->findAll();
    	$callboard = $em->getRepository('JiliApiBundle:CallBoard')->find($id);
    	$title = $request->request->get('title');
    	$author = $request->request->get('author');
    	$start_time = $request->request->get('start_time');
    	$content = $request->request->get('content');
        $category = $request->request->get('category');
    	if ($request->getMethod() == 'POST') {
    	    if($title && $start_time  && $author && $content){
    			$callboard->setTitle($title);
    			$callboard->setStartTime(date_create($start_time));
    			$callboard->setUpdateTime(date_create(date('Y-m-d H:i:s')));
    			$callboard->setAuthor($author);
    			$callboard->setContent($content);
                $callboard->setCbType($category);
    			$em->persist($callboard);
				$em->flush();
				return $this->redirect($this->generateUrl('_admin_infoCallboard'));
    		}else{
    			$codeflag = $this->container->getParameter('init_one');
    		}
    	}
    	return $this->render('JiliApiBundle:Admin:editCallboard.html.twig',array(
					'callboard'=>$callboard,
                    'cb_category'=>$cb_category,
		    		'codeflag'=>$codeflag,
		    		'title'=>$title,
		    		'start_time'=>$start_time,
		    		'content'=>	$content,
		    		'author'=>	$author,
					));
    }
    
    /**
     * @Route("/delCallboard/{id}", name="_admin_delCallboard")
     */
    public function delCallboardAction($id)
    {
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
    	$em = $this->getDoctrine()->getManager();
    	$callboard = $em->getRepository('JiliApiBundle:CallBoard')->find($id);
    	$em->remove($callboard);
    	$em->flush();
    	return $this->redirect($this->generateUrl('_admin_infoCallboard'));
    }
    
    
    
    /**
     * @Route("/infoCallboard", name="_admin_infoCallboard")
     */
    public function infoCallboardAction()
    {
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
    	$request = $this->get('request');
    	$em = $this->getDoctrine()->getManager();
    	$callboard = $em->getRepository('JiliApiBundle:CallBoard')->getCallboard();
    	$paginator = $this->get('knp_paginator');
    	$arr['pagination'] = $paginator
    	->paginate($callboard,
    			$request->query->get('page', 1), $this->container->getParameter('page_num'));
    	$arr['pagination']->setTemplate('JiliApiBundle::pagination.html.twig');
    	return $this->render('JiliApiBundle:Admin:infoCallboard.html.twig',$arr);
    }
    
    
    /**
     * @Route("/addCallboard", name="_admin_addCallboard")
     */
    public function addCallboardAction()
    {
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
    	$codeflag = $this->container->getParameter('init');
    	$callboard = new Callboard();
    	$em = $this->getDoctrine()->getManager();
    	$request = $this->get('request');
    	$title = $request->request->get('title');
    	$start_time = $request->request->get('start_time');
    	$content = $request->request->get('content');
    	$author = $request->request->get('author');
        $category = $request->request->get('category');
        $cb_category = $em->getRepository('JiliApiBundle:CbCategory')->findAll();
    	if ($request->getMethod() == 'POST') {
    		if($title && $start_time  && $author && $content){
    			$callboard->setTitle($title);
    			$callboard->setStartTime(date_create($start_time));
    			$callboard->setAuthor($author);
    			$callboard->setContent($content);
                $callboard->setCbType($category);
    			$em->persist($callboard);
				$em->flush();
				return $this->redirect($this->generateUrl('_admin_infoCallboard'));
    		}else{
    			$codeflag = $this->container->getParameter('init_one');
    		}
    	}
    	return $this->render('JiliApiBundle:Admin:addCallboard.html.twig',
    			array(
		    		'codeflag'=>$codeflag,
		    		'title'=>$title,
		    		'start_time'=>$start_time,
		    		'content'=>	$content,
		    		'author'=>$author,
                    'cb_category'=> $cb_category
					));
    	
    }

    /**
     * @Route("/delCbType/{id}", name="_admin_delCbType")
     */
    public function delCbTypeAction($id)
    {
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
        $em = $this->getDoctrine()->getManager();
        $cbcategory = $em->getRepository('JiliApiBundle:CbCategory')->find($id);
        $em->remove($cbcategory);
        $em->flush();
        return $this->redirect($this->generateUrl('_admin_infoCbType'));
    }
    

     /**
     * @Route("/editCbType/{id}", name="_admin_editCbType")
     */
    public function editCbTypeAction($id)
    {
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
        $codeflag = $this->container->getParameter('init');
        $request = $this->get('request');
        $em = $this->getDoctrine()->getManager();
        $cbcategory = $em->getRepository('JiliApiBundle:CbCategory')->find($id);
        $cbname = $request->request->get('categoryName');
        if ($request->getMethod() == 'POST') {
            if($cbname){
                $cbcategory->setCategoryName($cbname);
                $em->persist($cbcategory);
                $em->flush();
                return $this->redirect($this->generateUrl('_admin_infoCbType'));
            }else{
                $codeflag = $this->container->getParameter('init_one');
            }
        }
        return $this->render('JiliApiBundle:Admin:editCbType.html.twig',array(
                    'cbcategory'=>$cbcategory,
                    'codeflag'=>$codeflag
                    ));
    
    }

    /**
     * @Route("/infoCbType", name="_admin_infoCbType")
     */
    public function infoCbTypeAction()
    {
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
        $request = $this->get('request');
        $em = $this->getDoctrine()->getManager();
        $cbType = $em->getRepository('JiliApiBundle:CbCategory')->findAll();
        $paginator = $this->get('knp_paginator');
        $arr['pagination'] = $paginator
        ->paginate($cbType,
                $request->query->get('page', 1), $this->container->getParameter('page_num'));
        $arr['pagination']->setTemplate('JiliApiBundle::pagination.html.twig');
        return $this->render('JiliApiBundle:Admin:infoCbType.html.twig',$arr);              

    }

    /**
     * @Route("/addCbType", name="_admin_addCbType")
     */
    public function addCbTypeAction()
    {
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
        $codeflag = $this->container->getParameter('init');
        $cbcategory = new CbCategory();
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $cbname = $request->request->get('categoryName');
        if ($request->getMethod() == 'POST') {
            if($cbname){
                $cbcategory->setCategoryName($cbname);
                $em->persist($cbcategory);
                $em->flush();
                return $this->redirect($this->generateUrl('_admin_infoCbType'));
            }else{
                $codeflag = $this->container->getParameter('init_one');
            }
        }
        return $this->render('JiliApiBundle:Admin:addCbType.html.twig',array('codeflag'=>$codeflag));
                    

    }
    
    /**
     * @Route("/exchangeCsv", name="_admin_exchangeCsv")
     */
    public function exchangeCsvAction()
    {
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
    	$response = new Response();

        $request = $this->get('request');
        $start_time = $request->request->get('start_time');
        $end_time = $request->request->get('end_time');

    	$em = $this->getDoctrine()->getManager();
    	$exchange = $em->getRepository('JiliApiBundle:PointsExchange')->exchangeInfo();
    	$arr['exchange'] = $exchange;
    	$response =  $this->render('JiliApiBundle:Admin:exchangeCsv.html.twig',$arr);
    	$response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
//     	$response->headers->set("Content-type","application/vnd.ms-excel; charset=utf-8");
        $filename = "export".date("YmdHis").".csv";
        $response->headers->set('Content-Disposition', 'attachment; filename='.$filename);
//         $response->headers->set('Content-Transfer-Encoding', 'binary');
//         $response->headers->set("Expires","0");
//         $response->headers->set("Pragma","no-cache");
        return $response;
    	
    }


    public function exchangeOK($exchange_id,$email,$status,$points,$finish_time){
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
        $em = $this->getDoctrine()->getManager();
        $exchanges = $em->getRepository('JiliApiBundle:PointsExchange')->find($exchange_id);
        if(!$exchanges->getStatus()){
             $userInfo = $em->getRepository('JiliApiBundle:User')->findByEmail($email);
            if(strlen($userInfo[0]->getId())>1){
                $uid = substr($userInfo[0]->getId(),-1,1);
            }else{
                $uid = $userInfo[0]->getId();
            }
            switch($uid){
                case 0:
                    $po = new PointHistory00();
                    break;
                case 1:
                    $po = new PointHistory01();
                    break;
                case 2:
                    $po = new PointHistory02();
                    break;
                case 3:
                    $po = new PointHistory03();
                    break;
                case 4:
                    $po = new PointHistory04();
                    break;
                case 5:
                    $po = new PointHistory05();
                    break;
                case 6:
                    $po = new PointHistory06();
                    break;
                case 7:
                    $po = new PointHistory07();
                    break;
                case 8:
                    $po = new PointHistory08();
                    break;
                case 9:
                    $po = new PointHistory09();
                    break;
            }
            $po->setUserId($userInfo[0]->getId());
            $po->setPointChangeNum('-'.$points);
            $po->setReason($this->container->getParameter('init_eight'));
            $em->persist($po);
            $em->flush();
            // $exchanges = $em->getRepository('JiliApiBundle:PointsExchange')->find($exchange_id);
            
            $exchanges->setStatus($this->container->getParameter('init_one'));
            $exchanges->setFinishDate(date_create($finish_time));
            $em->persist($exchanges);
            $em->flush();
        }
        return true;
    }

    public function exchangeNG($exchange_id,$email,$status,$points,$finish_time){
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
        $em = $this->getDoctrine()->getManager();
        $exchanges = $em->getRepository('JiliApiBundle:PointsExchange')->find($exchange_id);
        if(!$exchanges->getStatus()){
            $userInfo = $em->getRepository('JiliApiBundle:User')->findByEmail($email);
            $user = $em->getRepository('JiliApiBundle:User')->find($userInfo[0]->getId());
            $user->setPoints(intval($user->getPoints()+$points));
            $em->persist($user);
            $em->flush();
            $exchanges->setStatus($this->container->getParameter('init_two'));
            $exchanges->setFinishDate(date_create($finish_time));
            $em->persist($exchanges);
            $em->flush();
        }   
        return true;
    }
    /**
     * @Route("/exchangeIn", name="_admin_exchangeIn")
     */
    public function exchangeInAction()
    {
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
        $success = '';
        $request = $this->get('request');
        if ($request->getMethod('post') == 'POST') {
            if (isset($_FILES['csv'])) {
                $file = $_FILES['csv']['tmp_name']; 
                $handle = fopen($file,'r'); 
                while ($data = fgetcsv($handle)){ 
                   $goods_list[] = $data;
                   unset($goods_list[0]);
                }
                foreach ($goods_list as $k=>$v){
                    $exchange_id = $v[0];
                    $email = iconv('gb2312','UTF-8//IGNORE',$v[1]);
                    $status = $v[6];
                    $finish_time = $v[7];
                    $points = $v[4];
                    if($status == 'ok'){
                        $this->exchangeOK($exchange_id,$email,$status,$points,$finish_time);
                    }else{
                        $this->exchangeNg($exchange_id,$email,$status,$points,$finish_time);       
                    }
                    $success = $this->container->getParameter('init_one');
                }
                fclose($handle);
            }
        }
        $arr['success'] = $success;
        return $this->render('JiliApiBundle:Admin:exchangeIn.html.twig',$arr);

    }
    
    /**
     * @Route("/exchangeInfo", name="_admin_exchangeInfo")
     */
    public function exchangeInfoAction()
    {
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
        $code = '';
        $start_time = '';
        $end_time = '';
        $arr['code'] = $code;
        $arr['start'] = $start_time;
        $arr['end'] = $end_time;
        $request = $this->get('request');
        if($this->getAdminIp())
          return $this->redirect($this->generateUrl('_default_error'));
    	$em = $this->getDoctrine()->getManager();
    	$exchange = $em->getRepository('JiliApiBundle:PointsExchange')->exchangeInfo('','');
    	$paginator  = $this->get('knp_paginator');
    	$arr['pagination'] = $paginator->paginate(
    			$exchange,
    			$this->get('request')->query->get('page', 1),
    			$this->container->getParameter('page_num')
    	);
    	$arr['pagination']->setTemplate('JiliApiBundle::pagination.html.twig');
        if ($request->getMethod() == 'POST'){
            $start_time = $request->request->get('start_time');
            $end_time = $request->request->get('end_time');
            if($request->request->get('add')){
                $response = new Response();   
                $exchange = $em->getRepository('JiliApiBundle:PointsExchange')->getExDateInfo($start_time,$end_time);
                $arr['exchange'] = $exchange;
                $response =  $this->render('JiliApiBundle:Admin:exchangeCsv.html.twig',$arr);
                $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
                $filename = "export".date("YmdHis").".csv";
                $response->headers->set('Content-Disposition', 'attachment; filename='.$filename);
                return $response;

                // }else{
                //     if(!$start_time && !$end_time){
                //         $exchange = $em->getRepository('JiliApiBundle:PointsExchange')->exchangeInfo();
                //         $arr['exchange'] = $exchange;
                //         $response =  $this->render('JiliApiBundle:Admin:exchangeCsv.html.twig',$arr);
                //         $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
                //         $filename = "export".date("YmdHis").".csv";
                //         $response->headers->set('Content-Disposition', 'attachment; filename='.$filename);
                //         return $response;

                //     }else{
                //         $code = $this->container->getParameter('init_one');
                //         $arr['code'] = $code;
                //     }
                // }
            }
            if($request->request->get('select')){ 
                $exchange = $em->getRepository('JiliApiBundle:PointsExchange')->exchangeInfo($start_time,$end_time);
                $paginator  = $this->get('knp_paginator');
                $arr['pagination'] = $paginator->paginate(
                        $exchange,
                        $this->get('request')->query->get('page', 1),
                        $this->container->getParameter('page_num')
                );
                $arr['pagination']->setTemplate('JiliApiBundle::pagination.html.twig');    
                
            }   
            $arr['start'] = $start_time;
            $arr['end'] = $end_time;
        }
        
    	return $this->render('JiliApiBundle:Admin:exchangeInfo.html.twig',$arr);
    }

    /**
     * @Route("/selectUser", name="_admin_selectUser")
     */
    public function selectUserAction(){
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
        $code = '';
        $count_user = '';
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $start_time = $request->request->get('start_time');
        $end_time = $request->request->get('end_time');
        $userCount = $em->getRepository('JiliApiBundle:User')->userCount();
        $count_user = $userCount[0]['num'];
        if ($request->getMethod() == 'POST'){
            $select_user = $em->getRepository('JiliApiBundle:User')->getUserCount($start_time,$end_time); 
            $count_user = $select_user[0]['num'];
        }
        return $this->render('JiliApiBundle:Admin:selectUser.html.twig',array('code'=>$code,'count_user'=>$count_user,'start_time'=>$start_time,'end_time'=>$end_time));
    }


    /**
     * @Route("/rewardRate", name="_admin_rewardRate")
     */
     public function rewardRateAction(){
        if($this->getAdminIp())
              return $this->redirect($this->generateUrl('_default_error'));  
        $search = array();
        $email = '';
        $edit = '';
        $multiple = '';
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $email = $request->request->get('email');
        if ($request->getMethod() == 'POST'){
            $user = $em->getRepository('JiliApiBundle:User')->getSearch($email);
            if($user){
                $search = $user[0];
                $edit = $request->request->get('edit');
                $multiple = $request->request->get('multiple');
                if($edit){
                    $editUser = $em->getRepository('JiliApiBundle:User')->find($search['id']);
                    $editUser->setRewardMultiple($multiple);
                    $em->persist($editUser);
                    $em->flush();
                    return $this->redirect($this->generateUrl('_admin_rewardRate'));
                }

            }
               
        }
        return $this->render('JiliApiBundle:Admin:rewardRate.html.twig',array('search'=>$search,'email'=>$email));  
    }
    
    /**
     * @Route("/index", name="_admin_index")
     */ 
    public function indexAction()
    {
        if($this->getAdminIp())
              return $this->redirect($this->generateUrl('_default_error'));
        return $this->render('JiliApiBundle:Admin:index.html.twig');
    }
    
    /**
     * @Route("/main", name="_admin_main")
     */
    public function mainAction()
    {
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
    	   return $this->render('JiliApiBundle:Admin:main.html.twig');
    }
    
    /**
     * @Route("/menu", name="_admin_menu")
     */
    public function menuAction()
    {
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
    	return $this->render('JiliApiBundle:Admin:menu.html.twig');
    }
    
    /**
     * @Route("/header", name="_admin_header")
     */
    public function headerAction()
    {
        if($this->getAdminIp())
            return $this->redirect($this->generateUrl('_default_error'));
    	return $this->render('JiliApiBundle:Admin:header.html.twig');
    }
    
    
    
}
