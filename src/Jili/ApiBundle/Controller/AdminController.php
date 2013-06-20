<?php
namespace Jili\ApiBundle\Controller;
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
     * @Route("/editPostion/{id}", name="_admin_editPostion")
     */
    public function editPostionAction($id)
    {
    	$codeflag = $this->container->getParameter('init');
    	$request = $this->get('request');
    	$em = $this->getDoctrine()->getManager();
    	$adver = $em->getRepository('JiliApiBundle:Advertiserment')->getAdvertiserment($id);
    	$position = $request->request->get('position');
    	$number = $request->request->get('number');
    	if ($request->getMethod() == 'POST') {
    		if($position && $number){
    			$adposition = $em->getRepository('JiliApiBundle:AdPosition')->find($id);
    			$adposition->setType($position);
    			$adposition->setPosition($number);
    			$em->persist($adposition);
    			$em->flush();
    			return $this->redirect($this->generateUrl('_admin_infoPostion'));
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
        		$adver->setIncentive($score);
        		$adver->setContent($info);
        		$adver->setInfo($rule);
        		$adver->setDeleteFlag($this->container->getParameter('init'));
        		$em->persist($adver);
        		$code = $adver->upload($path);
        		if(!$code){
        			$em->flush();
        			$adposition = new AdPosition();
        			$adposition->setAdId($adver->getId());
        			$adposition->setType($this->container->getParameter('init_one'));
        			$adposition->setPosition($this->container->getParameter('init'));
        			$em->persist($adposition);
        			$em->flush();
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
    	$adver = $em->getRepository('JiliApiBundle:Advertiserment')->getAdvertisermentList();
    	$paginator = $this->get('knp_paginator');
    	$arr['pagination'] = $paginator
    	->paginate($adver,
    			$request->query->get('page', 1), $this->container->getParameter('page_num'));
    	$arr['pagination']->setTemplate('JiliApiBundle::pagination.html.twig');
    	return $this->render('JiliApiBundle:Admin:infoAdver.html.twig',$arr);
    	 
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
    			$adver->setIncentive($score);
    			$adver->setContent($info);
    			$adver->setInfo($rule);
    			$adver->setDeleteFlag($this->container->getParameter('init'));
    			$em->persist($adver);
    			$code = $adver->upload($path);
    			if(!$code){
    				$em->flush();
    				return $this->redirect($this->generateUrl('_admin_infoAdver'));
    			}else{
    				if($code=='图片为必填项'){
    					$em->flush();
    					return $this->redirect($this->generateUrl('_admin_infoAdver'));
    				}
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
