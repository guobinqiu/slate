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
use Jili\ApiBundle\Entity\CallBoard;
use Jili\ApiBundle\Entity\LimitAd;
use Jili\ApiBundle\Entity\RateAd;
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
	/**
	 * @Route("/login", name="_admin_login")
	 */
    public function loginAction()
    {
    	$code = $this->container->getParameter('init');
    	$request = $this->get('request');
    	$username = $request->request->get('username');
    	$password = $request->request->get('password');
    	if ($request->getMethod() == 'POST') {
    		if($username=='admin' && $password=='admin'){
//     			$session = new Session();
//     			$session->start();
//     			$session->set('admin_name', $username);
    			$code = $this->container->getParameter('init');
    			return $this->redirect($this->generateUrl('_admin_index' ));
    		}else{
    			$code = $this->container->getParameter('init_one');
    		}
    	}
        return $this->render('JiliApiBundle:Admin:login.html.twig',array('code'=>$code));
    }
    
    //没有通过认证
    private function noCertified($userId,$adid){
    	$em = $this->getDoctrine()->getManager();
    	$adworder = $em->getRepository('JiliApiBundle:AdwOrder')->getOrderInfo($userId,$adid);
    	if(empty($adworder)){
    		return false;
    	}else{
    		$adworder = $em->getRepository('JiliApiBundle:AdwOrder')->find($adworder[0]['id']);
    		$adworder->setConfirmTime(date_create(date('Y-m-d H:i:s')));
    		$adworder->setOrderStatus($this->container->getParameter('init_four'));
    		$em->persist($adworder);
    		$em->flush();
    		return true;
    	}
    }
    //已经认证
    private function hasCertified($userId,$adid,$price){
    	$em = $this->getDoctrine()->getManager();
    	$adworder = $em->getRepository('JiliApiBundle:AdwOrder')->getOrderInfo($userId,$adid);
    	if(empty($adworder)){
    		return false;
    	}else{
    		$adworder = $em->getRepository('JiliApiBundle:AdwOrder')->find($adworder[0]['id']);
    		$adworder->setConfirmTime(date_create(date('Y-m-d H:i:s')));
    		$adworder->setOrderStatus($this->container->getParameter('init_three'));
    		$em->persist($adworder);
    		$em->flush();
    		if($adworder->getIncentiveType()==1){
    			$limitrs = new LimitAdResult();
    			$limitrs->setAccessHistoryId($adworder->getId());
    			$limitrs->setUserId($userId);
    			$limitrs->setLimitAdId($adid);
    			$limitrs->setResultIncentive($adworder->getIncentive());
    			$em->persist($limitrs);
    			$em->flush();
    			$user = $em->getRepository('JiliApiBundle:User')->find($userId);
    			$user->setPoints(intval($user->getPoints()+$adworder->getIncentive()));
    			$em->persist($user);
    			$em->flush();
    			$this->getPointHistory($userId,$adworder->getIncentive());
    		}else{
    			$raters = new RateAdResult();
    			$raters->setAccessHistoryId($adworder->getId());
    			$raters->setUserId($userId);
    			$raters->setRateAdId($adid);
    			$raters->setResultPrice($price);
    			$raters->setResultIncentive(intval($price*$adworder->getIncentiveRate()/100));
    			$em->persist($raters);
    			$em->flush();
    			$user = $em->getRepository('JiliApiBundle:User')->find($userId);
    			$user->setPoints(intval($user->getPoints()+$raters->getResultIncentive()));
    			$em->persist($user);
    			$em->flush();
    			$this->getPointHistory($userId,intval($price*$adworder->getIncentiveRate()/100));
    		}
    		return true;
    	}
    	
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
    
    private function getStatus($uid,$aid){
    	$em = $this->getDoctrine()->getManager();
    	$adwStatus = $em->getRepository('JiliApiBundle:AdwOrder')->getOrderStatus($uid,$aid);
    	if(empty($adwStatus))
    		return true;
    	else
    		return false;
    }
    
    
    
    /**
     * @Route("/importAdver", name="_admin_importAdver")
     */
    public function importAdverAction()
    {
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
                	if($this->getStatus($userId[1],$adid[1])){
                		if($status == '未通过'){
                			if(!$this->noCertified($userId[1],$adid[1])){
                				$code[] = $name.'-'.$userId[1].'-'.$adid[1].'插入数据失败';
                			}
                		}
                		if($status == '已认证'){
                			if(!$this->hasCertified($userId[1],$adid[1],$v[4])){
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
    	$em = $this->getDoctrine()->getManager();
    	$banner = $em->getRepository('JiliApiBundle:AdBanner')->find($id);
    	$em->remove($banner);
    	$em->flush();
    	return $this->redirect($this->generateUrl('_admin_infoBanner'));
    }
    
    
    /**
     * @Route("/infoBanner", name="_admin_infoBanner")
     */
    public function infoBannerAction()
    {
    	$request = $this->get('request');
    	$em = $this->getDoctrine()->getManager();
    	$adbanner = $em->getRepository('JiliApiBundle:AdBanner')->findAll();
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
    	$code ='';
    	$request = $this->get('request');
    	$url = $request->request->get('url');
    	$em = $this->getDoctrine()->getManager();
    	$adbanner = $em->getRepository('JiliApiBundle:AdBanner')->find($id);
    	$form  = $this->createForm(new EditBannerType(),$adbanner);
    	if ($request->getMethod() == 'POST') {
    		$form->bind($request);
    		$path =  $this->container->getParameter('upload_banner_dir');
    		$adbanner->setAdUrl($url);
    		$adbanner->setCreateTime(date_create(date('Y-m-d H:i:s')));
    		$em->persist($adbanner);
    		$code = $adbanner->upload($path);
    		if(!$code){
    			$em->flush();
    			return $this->redirect($this->generateUrl('_admin_infoBanner'));
    		}
    	}
    	return $this->render('JiliApiBundle:Admin:editBanner.html.twig',
    			array('banner'=>$adbanner,'form' => $form->createView(),'code'=>$code));
    
    }
    
    
    /**
     * @Route("/addPostion", name="_admin_addPostion")
     */
    public function addPostionAction()
    {
    	$postion = new AdPosition();
    	$codeflag = $this->container->getParameter('init');
    	$request = $this->get('request');
    	$aid = $request->request->get('aid');
    	$position = $request->request->get('position');
    	$number = $request->request->get('number');
    	$em = $this->getDoctrine()->getManager();
    	if ($request->getMethod() == 'POST') {
    		if($aid && $position && $number){
    			$adverment = $em->getRepository('JiliApiBundle:Advertiserment')->find($aid);
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
    					$postion->setAdId($aid);
    					$em->persist($postion);
    					$em->flush();
    					return $this->redirect($this->generateUrl('_admin_infoPostion'));
    				}
    			}
    		}else{
    			$codeflag = $this->container->getParameter('init_one');
    		}
    	}
    	return $this->render('JiliApiBundle:Admin:addPostion.html.twig',
    			array('codeflag'=>$codeflag,'aid'=>$aid,'position'=>$position,'number'=>$number));
    }
    
    /**
     * @Route("/delAdPosition/{id}", name="_admin_delAdPosition")
     */
    public function delAdPositionAction($id)
    {
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
    	$rule = $request->request->get('rule');
    	$form  = $this->createForm(new AddAdverType(),$adver);
    	if ($request->getMethod() == 'POST') {
    		if($title && $start_time && $end_time && $info && $comment && $rule && $url && $category && $score){ 
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
        			$adver->setIncentiveRate($score);
        		}
        		$adver->setContent($info);
        		$adver->setInfo($rule);
        		$adver->setDeleteFlag($this->container->getParameter('init'));
        		$em->persist($adver);
        		$code = $adver->upload($path);
        		if(!$code){
        			$em->flush();
//         			$adposition = new AdPosition();
//         			$adposition->setAdId($adver->getId());
//         			$adposition->setType($this->container->getParameter('init_one'));
//         			$adposition->setPosition($this->container->getParameter('init'));
//         			$em->persist($adposition);
//         			$em->flush();
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
    	$rule = $request->request->get('rule');
    	$form  = $this->createForm(new AddAdverType(),$adver);
    	if ($request->getMethod() == 'POST') {
    		if($title && $start_time && $end_time && $info && $comment && $rule && $url && $category && $score){
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
    				$adver->setIncentiveRate($score);
    			}
    			$adver->setIncentive($score);
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
    	$codeflag = $this->container->getParameter('init');
    	$request = $this->get('request');
    	$em = $this->getDoctrine()->getManager();
    	$callboard = $em->getRepository('JiliApiBundle:CallBoard')->find($id);
    	$title = $request->request->get('title');
    	$author = $request->request->get('author');
    	$start_time = $request->request->get('start_time');
    	$content = $request->request->get('content');
    	if ($request->getMethod() == 'POST') {
    	    if($title && $start_time  && $author && $content){
    			$callboard->setTitle($title);
    			$callboard->setStartTime(date_create($start_time));
    			$callboard->setUpdateTime(date_create(date('Y-m-d H:i:s')));
    			$callboard->setAuthor($author);
    			$callboard->setContent($content);
    			$em->persist($callboard);
				$em->flush();
				return $this->redirect($this->generateUrl('_admin_infoCallboard'));
    		}else{
    			$codeflag = $this->container->getParameter('init_one');
    		}
    	}
    	return $this->render('JiliApiBundle:Admin:editCallboard.html.twig',array(
					'callboard'=>$callboard,
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
    	$em = $this->getDoctrine()->getManager();
    	$callboard = $em->getRepository('JiliApiBundle:Callboard')->find($id);
    	$em->remove($callboard);
    	$em->flush();
    	return $this->redirect($this->generateUrl('_admin_infoCallboard'));
    }
    
    
    
    /**
     * @Route("/infoCallboard", name="_admin_infoCallboard")
     */
    public function infoCallboardAction()
    {
    	$request = $this->get('request');
    	$em = $this->getDoctrine()->getManager();
    	$callboard = $em->getRepository('JiliApiBundle:CallBoard')->findAll();
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
    	$codeflag = $this->container->getParameter('init');
    	$callboard = new Callboard();
    	$em = $this->getDoctrine()->getManager();
    	$request = $this->get('request');
    	$title = $request->request->get('title');
    	$start_time = $request->request->get('start_time');
    	$content = $request->request->get('content');
    	$author = $request->request->get('author');
    	if ($request->getMethod() == 'POST') {
    		if($title && $start_time  && $author && $content){
    			$callboard->setTitle($title);
    			$callboard->setStartTime(date_create($start_time));
    			$callboard->setAuthor($author);
    			$callboard->setContent($content);
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
		    		'author'=>$author
					));
    	
    }
    
    /**
     * @Route("/index", name="_admin_index")
     */
    public function indexAction()
    {
    	
    	return $this->render('JiliApiBundle:Admin:index.html.twig');
    }
    
    /**
     * @Route("/main", name="_admin_main")
     */
    public function mainAction()
    {
    	return $this->render('JiliApiBundle:Admin:main.html.twig');
    }
    
    /**
     * @Route("/menu", name="_admin_menu")
     */
    public function menuAction()
    {
    	return $this->render('JiliApiBundle:Admin:menu.html.twig');
    }
    
    /**
     * @Route("/header", name="_admin_header")
     */
    public function headerAction()
    {
    	return $this->render('JiliApiBundle:Admin:header.html.twig');
    }
    
    
    
}
