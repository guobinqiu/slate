<?php
namespace Jili\ApiBundle\Controller;

use Gregwar\CaptchaBundle\GregwarCaptchaBundle;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Cookie;
use Jili\ApiBundle\Form\FirstRegType;
use Jili\ApiBundle\Form\forgetPassType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Jili\ApiBundle\Form\RegType;
use Jili\ApiBundle\Form\CaptchaType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Jili\ApiBundle\Entity\User;
use Jili\ApiBundle\Entity\TaskOrder;
use Jili\ApiBundle\Entity\PointsExchange;
use Jili\ApiBundle\Entity\LoginLog;
use Jili\ApiBundle\Entity\setPasswordCode;
use Gregwar\Captcha\CaptchaBuilder;
use Jili\ApiBundle\Entity\TaskHistory00;
use Jili\ApiBundle\Entity\TaskHistory01;
use Jili\ApiBundle\Entity\TaskHistory02;
use Jili\ApiBundle\Entity\TaskHistory03;
use Jili\ApiBundle\Entity\TaskHistory04;
use Jili\ApiBundle\Entity\TaskHistory05;
use Jili\ApiBundle\Entity\TaskHistory06;
use Jili\ApiBundle\Entity\TaskHistory07;
use Jili\ApiBundle\Entity\TaskHistory08;
use Jili\ApiBundle\Entity\TaskHistory09;

class UserController extends Controller
{
	/**
	 * @Route("/checkFlag/{id}", name="_user_checkFlag")
	 */
	public function checkFlagAction($id){
		$em = $this->getDoctrine()->getManager();
		$user = $em->getRepository('JiliApiBundle:User')->find($id);
		return new Response($user->getIsInfoSet());
	}
	
	/**
	 * @Route("/checkPwd", name="_user_checkPwd")
	 */
	public function checkPwdAction(){
		$request = $this->get('request');
		$pwd = $request->query->get('pwd');
		$id = $this->get('request')->getSession()->get('uid');
		$em = $this->getDoctrine()->getManager();
		$user = $em->getRepository('JiliApiBundle:User')->find($id);
	    if($user->pw_encode($pwd) == $user->getPwd())
			$code = $this->container->getParameter('init');
		else
			$code = $this->container->getParameter('init_one');
		return new Response($code);
	}
	
	/**
	 * @Route("/updatePwd", name="_user_updatePwd")
	 */
	public function updatePwdAction(){
		return $this->render('JiliApiBundle:User:changePwd.html.twig');
	}
	
	/**
	 * @Route("/changePwd", name="_user_changePwd")
	 */
	public function changePwdAction(){
		$arr['codeflag'] = '';
		$arr['code'] = '';
		$request = $this->get('request');
		$oldPwd =  $request->request->get('oldPwd');
		$pwd = $request->request->get('pwd');
		$newPwd = $request->request->get('newPwd');
		$id = $this->get('request')->getSession()->get('uid');
		$em = $this->getDoctrine()->getManager();
		$user = $em->getRepository('JiliApiBundle:User')->find($id);
		if ($request->getMethod() == 'POST') {
			if($oldPwd){
				if($user->pw_encode($oldPwd) == $user->getPwd()){
					if($pwd){
						if(!preg_match("/^[0-9A-Za-z_]{6,20}$/",$pwd)){
							$arr['code'] = $this->container->getParameter('change_wr_pwd');
							$arr['codeflag'] = $this->container->getParameter('init_three');
						}else{
							if($pwd == $newPwd){
								$user->setPwd($pwd);
								$em->flush();
								$arr['codeflag'] = $this->container->getParameter('init_one');
								$arr['code'] = $this->container->getParameter('forget_su_pwd');
							}else{
								$arr['codeflag'] = $this->container->getParameter('init_four');
								$arr['code'] = $this->container->getParameter('change_unsame_pwd');
							}
						}
					}else{
						$arr['code'] = $this->container->getParameter('change_en_newpwd');
						$arr['codeflag'] = $this->container->getParameter('init_five');
					}
				}else{
					$arr['code'] = $this->container->getParameter('change_wr_oldpwd');
					$arr['codeflag'] = $this->container->getParameter('init_two');
				}
			}else{
				$arr['code'] = $this->container->getParameter('change_en_oldpwd');
				$arr['codeflag'] = $this->container->getParameter('init_six');
			}
    		
		}
		return $this->render('JiliApiBundle:User:changePwd.html.twig',$arr);
	}
	

	/**
	 * @Route("/resUp", name="_user_resUp")
	 */
	public function resUp(){
		$request = $this->get('request');
		if($request->getSession()->get('uid')){
			$id = $request->getSession()->get('uid');
			$em = $this->getDoctrine()->getManager();
			$user = $em->getRepository('JiliApiBundle:User')->find($id);
			if ($request->getMethod() == 'POST'){
				if($request->request->get('resize')){
					$resizePath = $request->request->get('resizePath');
					$x = $request->request->get('x');
					$y = $request->request->get('y');
					$x1 = $request->request->get('w');
					$y1 = $request->request->get('h');
					if(!$x)
						$x=0;
					if(!$y)
						$y=0;
					if(!$x1)
						$x1=256;
					if(!$y1)
						$y1=256;
					$user->resizeUpload($resizePath,$x,$y,$x1,$y1);
					$user->setIconPath($resizePath);
					$em->flush();
					
				}
				return $this->redirect($this->generateUrl('_user_info'));

			}	
		}
	}


	/**
	 * @Route("/info", name="_user_info",requirements={"_scheme"="https"})
	 */
	public function infoAction()
	{
		$code = '';
		$flag = '';
		$codeflag = '';
		$daydate =  date("Y-m-d H:i:s", strtotime(' -30 day'));
		$request = $this->get('request');
		$id = $request->getSession()->get('uid');
		$em = $this->getDoctrine()->getManager();
		$user = $em->getRepository('JiliApiBundle:User')->find($id);
		$option = array('daytype' => 0 ,'offset'=>'1','limit'=>'10');
		$adtaste = $this->selTaskHistory($id,$option);
		foreach ($adtaste as $key => $value) {
			if(($value['incentive']==0 || $value['orderStatus'] == 1) && strtotime($value['createTime']->format('Y-m-d H:i:s')) < strtotime($daydate)){
				unset($adtaste[$key]);
			}
		}
		$adtasteNum = count($adtaste);
		$exchange = $em->getRepository('JiliApiBundle:PointsExchange');
		$exchange = $exchange->getUserExchange($id,$option);
		$sex = $request->request->get('sex');
		$tel = $request->request->get('tel');
		$form  = $this->createForm(new RegType(), $user);
		if ($request->getMethod() == 'POST') {
			if($request->request->get('update')){
				if($sex){
					if(!preg_match("/^13[0-9]{1}[0-9]{8}$|15[0-9]{1}[0-9]{8}$|18[0-9]{1}[0-9]{8}$/",$tel)){
						$codeflag = $this->container->getParameter('update_wr_mobile');
					}else{
						$user->setSex($sex);
						$user->setTel($tel);
						// 	    	$em->persist($user);
						$user->setIsInfoSet($this->container->getParameter('init_one'));
						$em->flush();
					}
				}else{
					$codeflag = $this->container->getParameter('update_se_sex');
				}
			}else{
				if($request->request->get('reset')){
	    			return $this->redirect($this->generateUrl('_user_info'));
    				
	    		}else{
	    			$form->bindRequest($request);
	    			$path =  $this->container->getParameter('upload_tmp_dir');
	    			$code = $user->upload($path);
	    			if($code == 1){
	    				$code =  $this->container->getParameter('upload_img_type');
	    			} 
	    			if($code == 2){
	    				$code =  $this->container->getParameter('upload_img_size');
	    			}
		    		return new Response(json_encode($code));
	    		}

			}
			
			 
		}
		
		return $this->render('JiliApiBundle:User:info.html.twig',array( 
				'form' => $form->createView(),
				'form_upload' =>$form->createView(),
				'user' => $user,
				'adtaste' => $adtaste,
				'exchange' => $exchange,
				'code' => $code,
				'codeflag'=>$codeflag,
				'adtasteNum'=>$adtasteNum
				));
	}
	
	/**
	 * @Route("/upload", name="_user_upload")
	 */
	public function uploadAction(){
		return $this->redirect($this->generateUrl('_user_info',array('code'=>$code)));
	}
	
	/**
	 * @Route("/update", name="_user_update")
	 */
	public function updateAction()
	{
		$code = $this->container->getParameter('init');
		$id = $this->get('request')->getSession()->get('uid');
		$em = $this->getDoctrine()->getManager();
		$user = $em->getRepository('JiliApiBundle:User')->find($id);
// 		$form  = $this->createForm(new RegType(), $user);
		$request = $this->get('request');
		$ck = $request->query->get('ck');
		$tel = $request->query->get('tel');
	    if ($request->getMethod() == 'POST') {
			if($ck){
				if(!preg_match("/^13[0-9]{1}[0-9]{8}$|15[0189]{1}[0-9]{8}$|189[0-9]{8}$/",$tel)){
					$code = $this->container->getParameter('init_two');
				}else{
					$user->setSex($ck);
					$user->setTel($tel);
					// 	    	$em->persist($user);
					$user->setIsInfoSet($this->container->getParameter('init_one'));
					$em->flush();
				}
			}else{
				$code = $this->container->getParameter('init_one');
			}
		}
		return $this->redirect($this->generateUrl('_user_info'));
	}
	
	/**
	 * @Route("/loginOut", name="_user_loginOut")
	 */
	public function loginOutAction(){
		$this->get('request')->getSession()->remove('uid');
		$this->get('request')->getSession()->remove('nick');
		setcookie ("jili_uid", "", time() - 3600,'/');
		setcookie ("jili_nick", "", time() - 3600,'/');
//         $request = $this->get('request');
//         $cookies = $request->cookies;
//         if ($cookies->has('jili_uid'))
//         {
//         	$response = new Response();
//         	$response->headers->clearCookie('jili_uid','/');
//         	$response->headers->clearCookie('jili_nick','/');
//         	$response->send();
//         }
		return $this->redirect($this->generateUrl('_default_index'));
	}
	
	/**
	 * @Route("/resetPwd", name="_user_resetPwd")
	 */
	public function resetPwdAction(){
		return $this->render('JiliApiBundle:User:resetPwd.html.twig');
		
	}
	
	/**
	 * @Route("/pwdCheck", name="_user_pwdCheck")
	 */
	public function pwdCheckAction(){
		$request = $this->get('request');
		$email = $request->query->get('email');
		$pwd = $request->query->get('pwd');
		$em_email = $this->getDoctrine()
		->getRepository('JiliApiBundle:User')
		->findByEmail($email);
		if(!$em_email){
			$code = $this->container->getParameter('init_one');
		}else{
			$id = $em_email[0]->getId();
			$em = $this->getDoctrine()->getEntityManager();
			$user = $em->getRepository('JiliApiBundle:User')->find($id);
			if($user->pw_encode($pwd) != $user->getPwd()){
				$code = $this->container->getParameter('init_one');
			}else{
				$code = $this->container->getParameter('init');
			}
		}
		return new Response($code);
	}
	
	

	/**
	 * @Route("/login", name="_user_login",requirements={"_scheme"="https"})
	 */
	public function loginAction(){
		$session = new Session();
		$session->start();
		if($this->get('request')->getSession()->get('uid')){
            return $this->redirect($this->generateUrl('_default_index'));
        }
		$request = $this->get('request');
		$goToUrl = $request->headers->get('referer');
		if(substr($goToUrl, -10) != 'user/login'){
			$session->set('goToUrl', $goToUrl);
		}
		$code = '';
		$email = $request->request->get('email');
		$pwd = $request->request->get('pwd');
		if ($request->getMethod() == 'POST'){
			if($email){
				if (!preg_match("/^[A-Za-z0-9-_.+%]+@[A-Za-z0-9-.]+\.[A-Za-z]{2,4}$/",$email)){
					$code = $this->container->getParameter('login_wr_mail');
				}else{
					$em_email = $this->getDoctrine()
					->getRepository('JiliApiBundle:User')
					->findByEmail($email);
					if(!$em_email){
						$code = $this->container->getParameter('login_wr');
						// 		    		echo 'email is unexist!';
					}else{
						$id = $em_email[0]->getId();
						$em = $this->getDoctrine()->getEntityManager();
						$user = $em->getRepository('JiliApiBundle:User')->find($id);
						if($user->pw_encode($pwd) != $user->getPwd()){
							// 		    			echo 'pwd is error!';
							$code = $this->container->getParameter('login_wr');
						}else{
							
							if($request->request->get('remember_me')=='1'){
								setcookie("jili_uid", $id, time() + 3600 * 24 * 365,'/');
								setcookie("jili_nick",$user->getNick(), time() + 3600 * 24 * 365,'/');
// 								$response = new Response();
// 								$response->headers->setCookie(new Cookie('jili_uid', $id,(time() + 3600 * 24 * 365), '/'));
// 								$response->headers->setCookie(new Cookie('jili_nick', $user->getNick(),(time() + 3600 * 24 * 365), '/'));
// 								$response->send();
// 								$request = $this->get('request');
// 								$cookies = $request->cookies;
// 								if ($cookies->has('uid'))
// 								{
// 									var_dump($cookies->get('uid'));
// 								}
							}
							
							$session->set('uid', $id);
							$session->set('nick', $user->getNick());
							$user->setLastLoginDate(date_create(date('Y-m-d H:i:s')));
							$user->setLastLoginIp($this->get('request')->getClientIp());
							$em->flush();
							$em = $this->getDoctrine()->getManager();
							$loginlog = new Loginlog();
							$loginlog->setUserId($id);
							$loginlog->setLoginDate(date_create(date('Y-m-d H:i:s')));
							$loginlog->setLoginIp($this->get('request')->getClientIp());
							$em->persist($loginlog);
							$em->flush();
							$current_url = $request->getSession()->get('goToUrl');
							$request->getSession()->remove('goToUrl');
							return $this->redirect($current_url);
						}
					}
			    }
			}else{
			    $code = $this->container->getParameter('login_en_mail');
		    }
			
	    }
		return $this->render('JiliApiBundle:User:login.html.twig',array('code'=>$code,'email'=>$email));
	}
	
	/**
	 * @Route("/checkReg/{id}",requirements={"id" = "\d+"}, name="_user_checkReg")
	 */
	public function checkRegAction($id){
		$em = $this->getDoctrine()->getManager();
		$user = $em->getRepository('JiliApiBundle:User')->find($id);
		if($user)
			$info = $em->getRepository('JiliApiBundle:User')->getUserList($id);
		else
			return $this->redirect($this->generateUrl('_default_error'));
		$arr['gotoEmial'] = $user->gotomail($info[0]['email']);
		$arr['user'] = $info[0];
		return $this->render('JiliApiBundle:User:checkReg.html.twig',$arr);
	}
	
	/**
	 * @Route("/checkCaptcha", name="_user_checkCaptcha")
	 */
	public function checkCaptchaAction(){
		$request = $this->get('request');
		if($this->get('request')->getSession()->get('phrase') != $request->query->get('captcha'))
			$code = $this->container->getParameter('init_one');
		else{
			$this->get('request')->getSession()->remove('phrase');
			$code = $this->container->getParameter('init');
		}
		return new Response($code);
	}
	
	/**
	 * @Route("/checkEmail", name="_user_checkEmail")
	 */
	public function checkEmailAction(){
		$request = $this->get('request');
		$email = $request->query->get('email');
		$em = $this->getDoctrine()->getManager();
		$user = $em->getRepository('JiliApiBundle:User')->findByEmail($email);
	    if(empty($user))
	    	$code = $this->container->getParameter('init_one');
	    else 
	    	$code = $this->container->getParameter('init');
		return new Response($code);
	}
	
	/**
	 * @Route("/checkNick", name="_user_checkNick")
	 */
	public function checkNickAction(){
		$request = $this->get('request');
		$nick = $request->query->get('nick');
		$em = $this->getDoctrine()->getManager();
		$user = $em->getRepository('JiliApiBundle:User')->findByNick($nick);
		if(empty($user))
			$code = $this->container->getParameter('init_one');
		else
			$code = $this->container->getParameter('init');
		return new Response($code);
	}
	
	/**
	 * @Route("/reset", name="_user_reset")
	 */
	public function resetAction(){
		$code = '';
		$request = $this->get('request');
		$email = $request->query->get('email');
		$em = $this->getDoctrine()->getManager();
		$user = $em->getRepository('JiliApiBundle:User')->findByEmail($email);
		if(empty($user)){
			$code = $this->container->getParameter('chnage_no_email');
		}else{
			$nick = $user[0]->getNick();
			$id = $user[0]->getId();
			$passCode = $em->getRepository('JiliApiBundle:setPasswordCode')->findByUserId($id);
			$url = $this->generateUrl('_user_resetPass',array('code'=>$passCode[0]->getCode(),'id'=>$id),true);
			$em = $this->getDoctrine()->getManager();
			$user = $em->getRepository('JiliApiBundle:User')->find($id);
			if($this->sendMail_reset($url, $email,$nick)){
				$passCode[0]->setIsAvailable($this->container->getParameter('init_one'));
				$passCode[0]->setCreateTime(date_create(date('Y-m-d H:i:s')));
				$em->flush();
				$code = $this->container->getParameter('init_one');
			}
		}
		return new Response($code);
	}
	
	
	/**
	 * @Route("/resetPass/{code}/{id}", name="_user_resetPass")
	 */
	public function resetPassAction($code,$id){
		$arr['codeflag'] = $this->container->getParameter('init');
		$em = $this->getDoctrine()->getManager();
		$user = $em->getRepository('JiliApiBundle:User')->find($id);
		$arr['user'] = $user;
		$setPasswordCode = $em->getRepository('JiliApiBundle:setPasswordCode')->findOneByUserId($id);
		if($setPasswordCode->getIsAvailable()==0){
			return $this->render('JiliApiBundle::error.html.twig');
		}
		$arr['pwdcode'] = $setPasswordCode;
		$time = $setPasswordCode->getCreateTime();
		if(time()-strtotime($time->format('Y-m-d H:i:s')) >= 3600*24){
			return $this->render('JiliApiBundle::error.html.twig');
		}else{
			if($setPasswordCode->getCode() == $code){
				$request = $this->get('request');
				$pwd = $request->request->get('pwd');
				$newPwd = $request->request->get('newPwd');
				if ($request->getMethod() == 'POST'){
					if($pwd){
						if(!preg_match("/^[0-9A-Za-z_]{6,20}$/",$pwd)){
							$arr['codeflag'] = $this->container->getParameter('init_three');
							$arr['code'] = $this->container->getParameter('forget_wr_pwd');
						}else{
							if($pwd == $newPwd){
								$this->get('request')->getSession()->set('uid',$id);
								$this->get('request')->getSession()->set('nick',$user->getNick());
								$user->setPwd($pwd);
								$setPasswordCode->setIsAvailable($this->container->getParameter('init'));
								$em->persist($user);
								$em->persist($setPasswordCode);
								$em->flush();
								$arr['codeflag'] = $this->container->getParameter('init_one');
								$arr['code'] = $this->container->getParameter('forget_su_pwd');
							}else{
								$arr['codeflag'] = $this->container->getParameter('init_four');
								$arr['code'] = $this->container->getParameter('forget_unsame_pwd');
							}
						}
					}else{
						$arr['codeflag'] = $this->container->getParameter('init_two');
						$arr['code'] = $this->container->getParameter('forget_en_pwd');
					}
					
				}
				return $this->render('JiliApiBundle:User:resetPass.html.twig',$arr);
			}
		}
	}
	
	/**
	 * @Route("/reSend", name="_user_reSend")
	 */
	public function reSend(){
		$request = $this->get('request');
		$id = $request->query->get('id');
		$code = $request->query->get('code');
		$nick = $request->query->get('nick');
		$email = $request->query->get('email');
		$url = $this->generateUrl('_user_forgetPass',array('code'=>$code,'id'=>$id),true);
		if($this->sendMail($url, $email,$nick)){
			$code = $this->container->getParameter('init_one');
		}else{
			$code = $this->container->getParameter('init');
		}
		return new Response($code);
		
	}
	/**
	 * @Route("/activeEmail/{email}", name="_user_activeEmail")
	 */
	public function activeEmail($email){
		$em = $this->getDoctrine()->getManager();
		$user_email = $em->getRepository('JiliApiBundle:User')->findByEmail($email);
		$str = 'jiliactiveregister';
		$code = md5($user_email[0]->getId().str_shuffle($str));
		$url = $this->generateUrl('_user_forgetPass',array('code'=>$code,'id'=>$user_email[0]->getId()),true);
		if($this->sendMail($url,$email,$user_email[0]->getNick())){
			$setPasswordCode = $em->getRepository('JiliApiBundle:setPasswordCode')->findByUserId($user_email[0]->getId());
			$setPasswordCode[0]->setCode($code);
			$setPasswordCode[0]->setCreateTime(date_create(date('Y-m-d H:i:s')));
			$em->persist($setPasswordCode[0]);
			$em->flush();
			// 					echo 'success';
			return $this->redirect($this->generateUrl('_user_checkReg', array('id'=>$user_email[0]->getId()),true));
		}
	}
	
	public function issetReg($email){
		$em = $this->getDoctrine()->getManager();
		$is_pwd = $em->getRepository('JiliApiBundle:User')->isPwd($email);
		if($is_pwd){
			$code = $this->container->getParameter('init_one');//用户已注册
		}else{
			$code = $this->container->getParameter('init_two');//用户未注册	
		}
		return $code;
	}
	// public function wenwenEmail($email){
	// 	$em = $this->getDoctrine()->getManager();
	// 	$is_wenwen = $em->getRepository('JiliApiBundle:User')->isFromWenwen($email);
	// 	if(empty($is_wenwen)){
	// 		$is_wenwen_pwd = $em->getRepository('JiliApiBundle:User')->isWenwenPwd($email);
	// 		if($is_wenwen_pwd){
	// 			$code = $this->container->getParameter('init_one');//普通用户已注册
	// 		}else{
	// 			$code = $this->container->getParameter('init');//普通用户重新激活
	// 		}
	// 	}else{
	// 		$is_wenwen_pwd = $em->getRepository('JiliApiBundle:User')->isWenwenPwd($email);
	// 		if($is_wenwen_pwd){
	// 			$code = $this->container->getParameter('init_two');//91wenwen已注册
	// 		}else{
	// 			$code = $this->container->getParameter('init_three');//91wenwen未注册
	// 		}
	// 	}
	// 	return $code;
	// }
	
	/**
	 * @Route("/reg", name="_user_reg",requirements={"_scheme"="https"})
	 */
	public function regAction(){
		$code_nick = '';
		$code_cha = '';
		$code_email = '';
		$code_re = '';
		if($this->get('request')->getSession()->get('uid')){
            return $this->redirect($this->generateUrl('_default_index'));
        }
		$request = $this->get('request');
		$user = new User();
		$form = $this->createForm(new CaptchaType(), array());
		$email = $request->request->get('email');
		$nick = $request->request->get('nick');
		if ($request->getMethod() == 'POST'){
			    if($this->get('request')->getSession()->get('phrase') != $request->request->get('captcha')){
			    	$this->get('request')->getSession()->remove('phrase');
			    	// $code_cha = $this->container->getParameter('init_one');
			    	$code_cha = $this->container->getParameter('reg_wr_captcha');
			    }else{
			    	$this->get('request')->getSession()->remove('phrase');
			    	if($email){
			    		if (!preg_match("/^[A-Za-z0-9-_.+%]+@[A-Za-z0-9-.]+\.[A-Za-z]{2,4}$/",$email)){
        					// $code_email = $this->container->getParameter('init_two');
        					$code_email = $this->container->getParameter('reg_wr_mail');
        				}else{
        					$em = $this->getDoctrine()->getManager();
        					$user_email = $em->getRepository('JiliApiBundle:User')->findByEmail($email);
                    	    if($user_email){
                    	    	$wenwen = $this->issetReg($email);
                    	    	if($wenwen==$this->container->getParameter('init_one')){
                    	    		$code_email = $this->container->getParameter('reg_al_mail');               	
                    	    	}
                    	    	if($wenwen==$this->container->getParameter('init_two')){
                    	    		$code_re = $this->container->getParameter('init_one');
                    	    		$code_email = $this->container->getParameter('reg_noal_mail');
                    	    	}	
                    	    }else{
        						if($nick){
        							$user_nick = $em->getRepository('JiliApiBundle:User')->findByNick($nick);
        							if($user_nick)
        								$code_nick = $this->container->getParameter('reg_al_nick');
        							else{
        								if (!preg_match("/^[\x{4e00}-\x{9fa5}a-zA-Z0-9_]{2,20}$/u",$nick)){
        										// $code_nick = $this->container->getParameter('init_three');
        									$code_nick = $this->container->getParameter('reg_wr_nick');
        								}else{
        									$count = (strlen($nick) + mb_strlen($nick,'UTF8')) / 2;
        									if($count > 20)
        										$code_nick = $this->container->getParameter('reg_wr_nick');
        									else{
        										$user->setNick($request->request->get('nick'));
	        									$user->setEmail($request->request->get('email'));
	        									$user->setPoints($this->container->getParameter('init'));
	        									$user->setIsInfoSet($this->container->getParameter('init'));
	        									$em->persist($user);
	        									$em->flush();
	        									$str = 'jilifirstregister';
	        									$code = md5($user->getId().str_shuffle($str));
	        									$url = $this->generateUrl('_user_forgetPass',array('code'=>$code,'id'=>$user->getId()),true);
	        									if($this->sendMail($url, $user->getEmail(),$user->getNick())){
	        										$setPasswordCode = new setPasswordCode();
	        										$setPasswordCode->setUserId($user->getId());
	        										$setPasswordCode->setCode($code);
	        										$setPasswordCode->setIsAvailable($this->container->getParameter('init_one'));
	        										$em->persist($setPasswordCode);
	        										$em->flush();
	        										// 					echo 'success';
	        										
	        									    return $this->redirect($this->generateUrl('_user_checkReg', array('id'=>$user->getId()),true));
	        									}

        									}
        									
        								}
        							}
        						}else{
        							$code_nick = $this->container->getParameter('reg_en_nick');
        						}
        						
        					}
        				}
			    	}else{
			    		$code_email = $this->container->getParameter('reg_en_mail');
			    	}
			    }
		}
		return $this->render('JiliApiBundle:User:reg.html.twig',array(
				'form' => $form->createView(),
				'code_nick'=>$code_nick,
				'code_cha'=>$code_cha,
				'code_email'=>$code_email,
				'code_re'=>$code_re,
				'email'=>$email,
				'nick' =>$nick
				));
	}
	
	/**
	 * @Route("/agreement", name="_user_agreement")
	 */
	public function agreementAction(){
		return $this->render('JiliApiBundle:User:agreement.html.twig');
	}

	/**
	 * @Route("/captcha", name="_user_captcha")
	 */
	public function captchaAction(){
	    $builder = new CaptchaBuilder;
	    $builder->setBackgroundColor(255,255,255);
	    $builder->setMaxBehindLines(0);
	    $builder->setMaxFrontLines(0);
	    $builder->build();
	    header('Content-type: image/jpeg');
	    $builder->output();
	    $session = new Session();
	    $session->start();
	    $session->set('phrase', $builder->getPhrase());
	    exit;
	}
	
	/**
	 * @Route("/exchange/{type}", name="_user_exchange")
	 */
	public function exchangeAction($type){
		$id = $this->get('request')->getSession()->get('uid');
		$em = $this->getDoctrine()->getManager();
		$repository = $em->getRepository('JiliApiBundle:PointsExchange');
		$option = array('daytype' => $type ,'offset'=>'','limit'=>'');
		$exchange = $repository->getUserExchange($id,$option);
		
		$arr['exchange'] = $exchange;
		$user = $em->getRepository('JiliApiBundle:User')->find($id);
		$arr['user'] = $user;
		$paginator = $this->get('knp_paginator');
		$arr['pagination'] = $paginator
		        ->paginate($exchange,
				$this->get('request')->query->get('page', 1), $this->container->getParameter('page_num'));
		$arr['pagination']->setTemplate('JiliApiBundle::pagination.html.twig');
		return $this->render('JiliApiBundle:User:exchange.html.twig',$arr);
	}

	
	/**
	 * @Route("/adtaste/{type}", name="_user_adtaste")
	 */
	public function adtasteAction($type){
		$daydate =  date("Y-m-d H:i:s", strtotime(' -30 day'));
		$id = $this->get('request')->getSession()->get('uid');
		$em = $this->getDoctrine()->getManager();
		$option = array('daytype' => $type ,'offset'=>'','limit'=>'');
		$adtaste = $this->selTaskHistory($id,$option);
		foreach ($adtaste as $key => $value) {
			if(($value['incentive']==0 || $value['orderStatus'] == 1) && strtotime($value['createTime']->format('Y-m-d H:i:s')) < strtotime($daydate)){
				unset($adtaste[$key]);
			}
		}
		$arr['adtaste'] = $adtaste;
		$user = $em->getRepository('JiliApiBundle:User')->find($id);
		$arr['user'] = $user;
		$paginator = $this->get('knp_paginator');
		$arr['pagination'] = $paginator
		->paginate($adtaste,
				$this->get('request')->query->get('page', 1), $this->container->getParameter('page_num'));
		$arr['pagination']->setTemplate('JiliApiBundle::pagination.html.twig');
		return $this->render('JiliApiBundle:User:adtaste.html.twig',$arr);
	}
	
	/**
	 * @Route("/regSuccess", name="_user_regSuccess")
	 */
	public function regSuccessAction(){
		return $this->render('JiliApiBundle:User:regSuccess.html.twig');
	}
	
	
	/**
	 * @Route("/forgetPass/{code}/{id}", name="_user_forgetPass")
	 */
	public function forgetPassAction($code,$id){
		$code_pwd = '';
		$arr['code_pwd']  = $code_pwd;
		$code_que_pwd = '';
		$arr['code_que_pwd']  = $code_que_pwd;
		$em = $this->getDoctrine()->getManager();
		$user = $em->getRepository('JiliApiBundle:User')->find($id);
// 		$province = $em->getRepository('JiliApiBundle:ProvinceList')->findAll();
// 		$arr['province'] = $province;
		$arr['user'] = $user;
		$setPasswordCode = $em->getRepository('JiliApiBundle:setPasswordCode')->findOneByUserId($id);
		if($setPasswordCode->getIsAvailable()==0){
			return $this->render('JiliApiBundle::error.html.twig');
		}
		$arr['pwdcode'] = $setPasswordCode;
		$time = $setPasswordCode->getCreateTime();
        if(time()-strtotime($time->format('Y-m-d H:i:s')) >= 3600*24){
        	return $this->render('JiliApiBundle::error.html.twig');
        }else{
        	if($setPasswordCode->getCode() == $code){
        		$request = $this->get('request');
        		$pwd = $request->request->get('pwd');
        		$que_pwd = $request->request->get('que_pwd');
        		if ($request->getMethod() == 'POST'){
        			if($request->request->get('ck')=='1'){
        				if($pwd){
        					if(!preg_match("/^[0-9A-Za-z_]{6,20}$/",$pwd)){
        						$code_pwd = $this->container->getParameter('forget_wr_pwd');
        						$arr['code_pwd']  = $code_pwd;
        					}else{
        						if($pwd == $que_pwd){
        							$this->get('request')->getSession()->set('uid',$id);
        							$this->get('request')->getSession()->set('nick',$user->getNick());
        							$user->setPwd($request->request->get('pwd'));
        							$setPasswordCode->setIsAvailable($this->container->getParameter('init'));
        							$em->persist($user);
        							$em->persist($setPasswordCode);
        							$em->flush();
//         							return $this->redirect($this->generateUrl('_user_regSuccess'));
        							return $this->render('JiliApiBundle:User:regSuccess.html.twig',$arr);
        						}else{
        							$code_que_pwd = $this->container->getParameter('forget_unsame_pwd');
        							$arr['code_que_pwd']  = $code_que_pwd;
        						}
        					}
        				}else{
        					$code_pwd = $this->container->getParameter('forget_en_pwd');
        					$arr['code_pwd']  = $code_pwd;
        				}
        			}else{
        				echo 'choose agree';
        			}
        		}
        		return $this->render('JiliApiBundle:User:forgetPass.html.twig',$arr);
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
		$nick = '';
		$url = $this->generateUrl('_user_forgetPass',array('code'=>$code,'id'=>$id),true);
		$em = $this->getDoctrine()->getManager();
		$user = $em->getRepository('JiliApiBundle:User')->find($id);
		if($this->sendMail($url, $email,$nick)){
			$setPasswordCode = new setPasswordCode();
			$setPasswordCode->setUserId($user->getId());
			$setPasswordCode->setCode($code);
			$em->persist($setPasswordCode);
		    $em->flush();
			echo 'success';
		}

    	return $this->render('JiliApiBundle:User:mission.html.twig');
	}
	
	//reset pwd send mail
	public function sendMail_reset($url,$email,$nick){
		$message = \Swift_Message::newInstance()
		->setSubject('积粒网-帐号密码重置')
		->setFrom(array('account@91jili.com'=>'积粒网'))
		->setTo($email)
		->setBody(
				'<html>' .
				' <head></head>' .
				' <body>' .
				'亲爱的'.$nick.'<br/>'.
				'<br/>'.
				'  我们收到您因为忘记密码，要求重置积粒网帐号密码的申请，请点击<a href='.$url.' target="_blank">这里</a>重置您的密码。<br/><br/>' .
				'  如果您并未提交重置密码的申请，请忽略本邮件，并关注您的账号安全，因为可能有其他人试图登录您的账户。<br/><br/>积粒网运营中心' .
				' </body>' .
				'</html>',
				'text/html'
		);
		$flag = $this->get('mailer')->send($message);
		if($flag===1){
			return true;
		}else{
			return false;
		}
	
	}
	
	

	public function sendMail($url,$email,$nick){
		$message = \Swift_Message::newInstance()
		->setSubject('积粒网-注册激活邮件')
		->setFrom(array('account@91jili.com'=>'积粒网'))
		->setTo($email)
		->setBody(
				        '<html>' .
						' <head></head>' .
						' <body>' .
				        '亲爱的'.$nick.'<br/>'.
				        '<br/>'.
						'  感谢您注册成为“积粒网”会员！请点击<a href='.$url.' target="_blank">这里</a>，立即激活您的帐户！<br/><br/>' .
						'  积粒网，一站式积分媒体！<br/>赚米粒，攒米粒，花米粒，一站搞定！' .
						' </body>' .
						'</html>',
						'text/html'
		);
		$flag = $this->get('mailer')->send($message);
		if($flag===1){
			return true;
		}else{
			return false;
		}
	
	}

	private function selTaskHistory($userid,$option){
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
      //$option = array('daytype' => '' ,'offset'=>'','limit'=>'');
      $po = $task->getUseradtaste($userid,$option);

      foreach ($po as $key => $value) {
			if($value['type']==1){
				$adUrl = $task->getUserAdwId($value['orderId']);
				$po[$key]['adid'] = $adUrl[0]['adid'];
			}else{
				$po[$key]['adid'] = '';
			}
		}
		return $po;

    }
	
}
