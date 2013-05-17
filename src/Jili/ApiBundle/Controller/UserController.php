<?php
namespace Jili\ApiBundle\Controller;

use Jili\ApiBundle\Form\FirstRegType;

use Jili\ApiBundle\Form\forgetPassType;

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
		$request = $this->get('request');
		
		$email = $request->request->get('email');
		$pwd = $request->request->get('pwd');
		$em = $this->getDoctrine()
		->getRepository('JiliApiBundle:User')
		->findByEmail($email);
		$request = $this->get('request');
		if ($request->getMethod() == 'POST'){
			if(!$em){
				echo 'email is unexist!';
			}else{
				$id = $em[0]->getId();
				$em1 = $this->getDoctrine()
				->getRepository('JiliApiBundle:User')
				->findById($id);
				if($em1[0]->pw_encode($pwd) != $em1[0]->getPwd()){
					echo 'pwd is error!';
				}else{
					$em = $this->getDoctrine()->getManager();
					$loginInfo = $em->getRepository('JiliApiBundle:LoginLog')->find($id);
					if(!$loginInfo){
						$loginlog = new Loginlog();
						$loginlog->setUserId($id);
						$loginlog->setLoginDate(date_create(date('Y-m-d H:i:s')));
						$loginlog->setLoginIp($this->get('request')->getClientIp());
						$em = $this->getDoctrine()->getManager();
						$em->persist($loginlog);
						$em->flush();
					}
					$loginInfo->setLoginDate(date_create(date('Y-m-d H:i:s')));
					$loginInfo->setLoginIp($this->get('request')->getClientIp());
					$em->flush();
					echo 'success!';
				}
			
			}
		}
		return $this->render('JiliApiBundle:User:login.html.twig',array(
// 				'form' => $form->createView(),
				));
	}
	
	/**
	 * @Route("/checkReg/{id}", name="_user_checkReg")
	 */
	public function checkRegAction($id){
		$em = $this->getDoctrine()->getManager();
		$user = $em->getRepository('JiliApiBundle:User')->find($id);
		$arr['gotoEmial'] = $user->gotomail($user->getEmail());
		return $this->render('JiliApiBundle:User:checkReg.html.twig',$arr);
	}
	
	
	/**
	 * @Route("/reg", name="_user_reg")
	 */
	public function regAction(){
		$user = new User();
		
// 		$form = $this->createForm(new FirstRegType() , $user);
		$request = $this->get('request');
		
		if ($request->getMethod() == 'POST'){
			// 			$form->bind($request);
			if($request->request->get('ck')==1){
				$em = $this->getDoctrine()->getManager();
				$user->setNick($request->request->get('nick'));
				$user->setEmail($request->request->get('email'));
// 				$user->setLastLoginIp($_SERVER["REMOTE_ADDR"]);
				$user->setFlag(0);
				$em->persist($user);
				$em->flush();
				$str = 'jilifirstregister';
				$code = md5($user->getId().str_shuffle($str));
				$url = $this->generateUrl('_user_forgetPass',array('code'=>$code,'id'=>$user->getId()),true);
				if($this->sendMail($url, $user->getEmail())){
					$user->setCode($code);
					$em->persist($user);
					$em->flush();
// 					echo 'success';
					return $this->redirect($this->generateUrl('_user_checkReg', array('id'=>$user->getId()),true));
				}
			}else{
				echo 'read and agree to the "plot grain network Member Terms and Conditions"';
			}
		}
				
		return $this->render('JiliApiBundle:User:reg.html.twig',array(
// 				'form' => $form->createView(),
				));
	}
	
	/**
	 * @Route("/exchange/{id}", name="_user_exchange")
	 */
	public function exchangeAction($id){
		$em = $this->getDoctrine()->getManager();
		$repository = $em->getRepository('JiliApiBundle:PointsExchange');
		$exchange = $repository->getUserExchange($id);
		$arr['exchange'] = $exchange;
		$paginator = $this->get('knp_paginator');
		$arr['pagination'] = $paginator
		        ->paginate($exchange,
				$this->get('request')->query->get('page', 1), $this->container->getParameter('page_num'));
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
	 * @Route("/forgetPass/{code}/{id}", name="_user_forgetPass")
	 */
	public function forgetPassAction($code,$id){
		$em = $this->getDoctrine()->getManager();
		$user = $em->getRepository('JiliApiBundle:User')->find($id);
		$form = $this->createForm(new forgetPassType(), $user);
		$time = $user->getLastLoginDate();
        if(time()-strtotime($time->format('Y-m-d H:i:s')) >= 3600){
        	echo '链接失效';
        }else{
        	if($user->getCode() == $code){
        		$request = $this->get('request');
        		if ($request->getMethod() == 'POST'){
        			if($user->getPwd()==''){
        				echo '第一次注册';
        			}else{
        				echo '重置密码';
        			}
        			$form->bind($request);
        			$user->setPwd($request->request->get('pwd'));
        			$em->persist($user);
        			$em->flush();
        		}
        		return $this->render('JiliApiBundle:User:forgetPass.html.twig',array(
        				'form' => $form->createView(),
        				'user' =>$user
        		));
        	}
        	
        }

		
	}
	    
	/**
	* @Route("/mission/{id}", name="_user_mission")
	*/
	public function missionAction($id){
//         $id =1;
		$str = 'jiliforgetpassword';
		$code = md5($id.str_shuffle($str));
// 		$request = $this->get('request');
		$email = '278583642@qq.com';
		$url = $this->generateUrl('_user_forgetPass',array('code'=>$code,'id'=>$id),true);
		$em = $this->getDoctrine()->getManager();
		$user = $em->getRepository('JiliApiBundle:User')->find($id);
		if($this->sendMail($url, $email)){
			$user->setCode($code);
			$em->persist($user);
		    $em->flush();
			echo 'success';
		}

    	return $this->render('JiliApiBundle:User:mission.html.twig');
	}

	public function sendMail($url,$email){
		$endTime = date('Y-m-d H:i:s');
		$message = \Swift_Message::newInstance()
		->setSubject('set pwd')
		->setFrom('quickresearch_1@163.com')
		->setTo($email)
		->setBody(
				$this->renderView(
						'JiliApiBundle:User:email.txt.twig',array(
								'end_time'=>$endTime,
								'url' => $url
								)
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
