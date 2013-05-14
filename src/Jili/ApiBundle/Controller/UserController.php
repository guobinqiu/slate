<?php
namespace Jili\ApiBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Jili\ApiBundle\Form\RegType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Jili\ApiBundle\Entity\User;
use Jili\ApiBundle\Entity\PointsExchange;
use Jili\ApiBundle\Entity\LoginLog;
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



class UserController extends Controller
{
	
	/**
	 * @Route("/info/{id}", name="_user_info")
	 */
	public function infoAction($id)
	{
		$em = $this->getDoctrine()->getManager();
		$user = $em->getRepository('JiliApiBundle:User')->find($id);
		$form  = $this->createForm(new RegType(), $user);
		$adtaste = $em->getRepository('JiliApiBundle:AdwAccessRecord');
		$adtaste = $adtaste->getUseradtaste($id);
		$exchange = $em->getRepository('JiliApiBundle:PointsExchange');
		$exchange = $exchange->getUserExchange($id);
		return $this->render('JiliApiBundle:User:info.html.twig',array( 
				'form' => $form->createView(),
				'form_upload' =>$form->createView(),
				'user' => $user,
				'adtaste' => $adtaste,
				'exchange' => $exchange,
				));
	}
	
	
	/**
	 * @Route("/upload/{id}", name="_user_upload")
	 */
	public function uploadAction($id){
		$request = $this->get('request');
		if ($request->getMethod() == 'POST') {
			$em = $this->getDoctrine()->getManager();
			$user = $em->getRepository('JiliApiBundle:User')->find($id);
			$form  = $this->createForm(new RegType(), $user);
			$form->bind($request);
			$path =  $this->container->getParameter('upload_tmp_dir');
			$em->persist($user);
			$em->flush();
			$user->upload($path);
			$em->flush();
		}

		return $this->redirect($this->generateUrl('_user_info',array('id'=>$user->getId())));
	}
	
	/**
	 * @Route("/update/{id}", name="_user_update")
	 */
	public function updateAction($id)
	{
// 		$request = $this->get('request');
// 		if ($request->getMethod() == 'POST'){
// 	    	$nick = $request->getParameter("nick");
// 	    	$pwd = $request->getParameter("pwd");
// 	    	$sex = $request->getParameter("sex");
// 	    	$birthday = $request->getParameter("birthday");
// 	    	$email = $request->getParameter("email");
// 	    	$tel= $request->getParameter("tel");
// 	    	$city = $request->getParameter("city");
// 	    	$point = $request->getParameter("point");
		$em = $this->getDoctrine()->getManager();
		$user = $em->getRepository('JiliApiBundle:User')->find($id);
		$form  = $this->createForm(new RegType(), $user);
		$request = $this->get('request');
		if ($request->getMethod() == 'POST') {
	    	$user->setNick($request->request->get('nick'));
	    	$user->setSex($request->request->get('sex'));
	    	$user->setBirthday($request->request->get('birthday'));
	    	$user->setTel($request->request->get('tel'));
	    	$user->setCity($request->request->get('city'));
	    	$user->setEducation($request->request->get('education'));
	    	$user->setProfession($request->request->get('profession'));
	    	$user->setHobby($request->request->get('hobby'));
	    	$user->setPersonalDes($request->request->get('personalDes'));
	    	$user->setFlag(1);
	    	$em->persist($user);
	    	$em->flush();
		}
		return $this->render('JiliApiBundle:User:update.html.twig',array('user' => $user));
	}
	

	/**
	 * @Route("/login", name="_user_login")
	 */
	public function loginAction(){
		//     	$user = new User();
		//     	$form = $this->createForm(new LoginType() , $user);
		//     	$form->bind($request);
		$nick = 'test';
		//     	$request = $this->get('request');
		//     	$nick = $request->getParameter("nick");
		//     	$pwd = $request->getParameter("pwd");
		$pwd = '123456';
		$em = $this->getDoctrine()
		->getRepository('JiliApiBundle:User')
		->findByNick($nick);
		if(!$em){
			echo 'nick is unexist!';
		}else{
			$id = $em[0]->getId();
			$em1 = $this->getDoctrine()
			->getRepository('JiliApiBundle:User')
			->findById($id);
			if($pwd != $em1[0]->getPwd()){
				echo 'pwd is error!';
			}else{
				$em = $this->getDoctrine()->getManager();
				$loginInfo = $em->getRepository('JiliApiBundle:LoginLog')->find($id);
				if(!$loginInfo){
					$loginlog = new Loginlog();
					$loginlog->setUserId($id);
					$loginlog->setLoginDate(date('Y-m-d H:i:s'));
					$loginlog->setLoginIp($_SERVER["REMOTE_ADDR"]);
					$em = $this->getDoctrine()->getManager();
					$em->persist($loginlog);
					$em->flush();
				}
				$loginInfo->setLoginDate(date('Y-m-d H:i:s'));
				$loginInfo->setLoginIp($_SERVER["REMOTE_ADDR"]);
				$em->flush();
				echo 'success!';
			}
			 
		}
		//     	$request = $this->get('request');
		//     	if ($request->getMethod() == 'POST'){
		//     		$form->bindRequest($request);
		//     	}
	
		return $this->render('JiliApiBundle:User:login.html.twig',array(
				'form' => $form->createView(),));
	}
	
	/**
	 * @Route("/reg", name="_user_reg")
	 */
	public function regAction(){
		$request = $this->get('request');
		if ($request->getMethod() == 'POST'){
			$user = new User();
			$form = $this->createForm(new RegType() , $user);
			$form->bind($request);
			if($form->isvalid()){
				$em = $this->getDoctrine()->getManager();
				$time = time();
				$user->setRegisterDate($time);
				$user->setLastLoginDate($time);
				$user->setLastLoginIp($_SERVER["REMOTE_ADDR"]);
				$user->setDeleteFlag(1);
				$em->persist($user);
				$em->flush();
				
			}
		}
// 		    	$user = new User();
// 		    	$user->setNick('test');
// 		    	$user->setPwd('1234567');
// 		    	$user->setSex(1);
// 		    	$user->setBirthday('2013-01-01 00:00:00');
// 		    	$user->setEmail('aasfa');
// 		    	$user->setIsEmailConfirmed('1');
// 		    	$user->setTel('12143124');
// 		    	$user->setIsTelConfirmed('1');
// 		    	$user->setCity('1');
// 		    	$user->setIdentityNum('11');
// 		    	$user->setRegisterDate(new \DateTime('2013-01-01 00:00:00'));
// 		    	$user->setLastlogindate(new \DateTime('2013-01-01 00:00:00'));
// 		    	$user->setLastloginip('192.168.1.1');
// 		    	$user->setPoints('22');
// 				$em->persist($user);
// 				$em->flush();

		
		
		
				//$history_id = $user->getId();
// 				$history_id = 7;
// 				if(strlen($history_id)!=1)
// 				    $history_id = substr($history_id,0,-1);

// 				$pointHistory->setUserId($history_id);
// 				$pointHistory->setPointChangeNum(50);
// 				$pointHistory->setReason(1);
// 				$em->persist($pointHistory);
// 				$em->flush();
				
		return $this->render('JiliApiBundle:User:reg.html.twig',array(
				'form' => $form->createView(),));
	}
	
	/**
	 * @Route("/exchange/{id}", name="_user_exchange")
	 */
	public function exchangeAction($id){
		$em = $this->getDoctrine()->getManager();
		$repository = $em->getRepository('JiliApiBundle:PointsExchange');
		$exchange = $repository->getUserExchange($id);
		$arr['exchange'] = $exchange;
// 		$paginator = $this->get('knp_paginator');
// 		$arr['pagination'] = $paginator
// 		        ->paginate($exchange,
// 				$this->get('request')->query->get('page', 1), $this->container->getParameter('page_num'));
		return $this->render('JiliApiBundle:User:exchange.html.twig',$arr);
	}
	
	
	/**
	 * @Route("/adtaste/{id}", name="_user_adtaste")
	 */
	public function adtasteAction($id){
		$em = $this->getDoctrine()->getManager();
		$repository = $em->getRepository('JiliApiBundle:AdwAccessRecord');
		$adtaste = $repository->getUseradtaste($id);
		$arr['adtaste'] = $adtaste;
		return $this->render('JiliApiBundle:User:adtaste.html.twig',$arr);
	}
	
	
	
	/**
	* @Route("/mission", name="_user_mission")
	*/
	public function missionAction($id){
// 		$str = '';
// 		$code = md5($id.$str);
		$request = $this->get('request');
		$user = new User();
		$nick = 'test';
		$email = '278583642@qq.com';
		if($this->sendMail($nick, $email)){
			
		}

    	return $this->render('JiliApiBundle:User:mission.html.twig');
	}

	public function sendMail($nick,$email){
		$message = \Swift_Message::newInstance()
		->setSubject('set pwd')
		->setFrom('quickresearch_1@163.com')
		->setTo($email)
		->setBody(
				$this->renderView(
						'JiliApiBundle:User:email.txt.twig',array('name' => $nick)
				)
		);
		$flag = $this->get('mailer')->send($message);
		if($flag===1){
			return true;
		}else{
			return false;
		}
	
	}
	
	
	
	
	
}
