<?php
namespace Jili\ApiBundle\Controller;
use Jili\ApiBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Cookie;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Jili\ApiBundle\Mailer;
use Jili\ApiBundle\Form\RegType;
use Jili\ApiBundle\Entity\LoginLog;

class DefaultController extends Controller
{
	
	/**
	 * @Route("/", name="_default_index",requirements={"_scheme"="https"})
	 * 
	 */ 
    public function indexAction()
    {
    	$request = $this->get('request');
        $cookies = $request->cookies;
        if ($cookies->has('jili_uid') &&  $cookies->has('jili_nick')){
            $this->get('request')->getSession()->set('uid',$cookies->get('jili_uid'));
            $this->get('request')->getSession()->set('nick',$cookies->get('jili_nick'));
    	}     
    	$arr['user'] = array();
        $em = $this->getDoctrine()->getManager();
        if( $this->get('request')->getSession()->get('uid')){
        	$user = $em->getRepository('JiliApiBundle:User')->find($this->get('request')->getSession()->get('uid'));
        	$arr['user'] = $user;
        }
        $code = $this->container->getParameter('init');
        $arr['userInfo'] = array();
        $email = $request->request->get('email');
        $arr['email'] = $email;
        $pwd = $request->request->get('pwd');
        $em_email = $this->getDoctrine()
        ->getRepository('JiliApiBundle:User')
        ->findByEmail($email);
        if ($request->getMethod() == 'POST'){
            if(!$em_email){
            	//echo 'email is unexist!';
            	$code = $this->container->getParameter('init_one');
            }else{
            	$id = $em_email[0]->getId();
            	$em = $this->getDoctrine()->getEntityManager();
            	$user = $em->getRepository('JiliApiBundle:User')->find($id);
            	if($user->pw_encode($pwd) != $user->getPwd()){
            		// 					echo 'pwd is error!';
            		$code = $this->container->getParameter('init_two');
            	}else{
//             		$session = new Session();
//             		$session->start();
            		if($request->request->get('remember_me')=='1'){
            			setcookie("jili_uid", $id, time() + 3600 * 24 * 365,'/');
            			setcookie("jili_nick",$user->getNick(), time() + 3600 * 24 * 365,'/');
            		}
            		$request->getSession()->set('uid', $id);
            		$request->getSession()->set('nick', $user->getNick());
//             		$session->set('uid', $id);
//             		$session->set('nick', $user->getNick());
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
            		return $this->redirect($this->generateUrl('_default_index'));
            	}
            }
        }
        $arr['code'] = $code;
		$repository = $em->getRepository('JiliApiBundle:Advertiserment');
		$advertiseBanner = $em->getRepository('JiliApiBundle:AdBanner')->findAll();
		$advertise = $repository->getAdvertiserList();
		$callboard = $em->getRepository('JiliApiBundle:Callboard')->findAll();
		$arr['callboard'] =  $callboard;
		$arr['advertise_banner'] = $advertiseBanner;
    	$arr['advertise'] = $advertise;
        return $this->render('JiliApiBundle:Default:index.html.twig',$arr);
    }
    
    /**
     * @Route("/fastLogin", name="_default_fastLogin")
     */
    function fastLoginAction(){
    	$code = $this->container->getParameter('init');
    	$arr['userInfo'] = array();
    	$request = $this->get('request');
    	$email = $request->query->get('email');
    	$pwd = $request->query->get('pwd');
    	$em_email = $this->getDoctrine()
    	->getRepository('JiliApiBundle:User')
    	->findByEmail($email);
//     	if ($request->getMethod() == 'POST'){
			if(!$em_email){
				//echo 'email is unexist!';
				$code = $this->container->getParameter('init_one');
			}else{
				$id = $em_email[0]->getId();
				$em = $this->getDoctrine()->getEntityManager();
				$user = $em->getRepository('JiliApiBundle:User')->find($id);
				if($user->pw_encode($pwd) != $user->getPwd()){
// 					echo 'pwd is error!';
					$code = $this->container->getParameter('init_two');
				}else{
					$session = new Session();
					$session->start();
					if($request->query->get('remember_me')=='1'){
						setcookie("jili_uid", $id, time() + 3600 * 24 * 365,'/');
						setcookie("jili_nick",$user->getNick(), time() + 3600 * 24 * 365,'/');
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
// 					return $this->redirect($this->generateUrl('_default_index'));
				}
			}
//     	}
    	return new Response($code);

    }
    
    /**
     * @Route("/landing", name="_default_landing",requirements={"_scheme"="https"})
     */
    public function landingAction(){
    	$code = '';
    	$is_user = '';
    	$request = $this->get('request');
    	$email = $request->query->get('email');
    	$fornick = $request->query->get('nick');
    	if(!$fornick)
    		$fornick = '';
    	if($email)
    		$request->getSession()->set('email',$email);
    	$u_email = $request->getSession()->get('email');
    	if(!$u_email)
    		return $this->redirect($this->generateUrl('_default_index'));
    	$nick = $request->request->get('nick');
    	$pwd = $request->request->get('pwd');
    	$newPwd = $request->request->get('newPwd');
    	$em = $this->getDoctrine()->getManager();
    	$is_from_wenwen = $em->getRepository('JiliApiBundle:User')->findByEmail($u_email);
    	if(!empty($is_from_wenwen)){
    		if($is_from_wenwen[0]->getPwd())
    			$is_user = $this->container->getParameter('init_one');
    		else
    			$is_user = $this->container->getParameter('init_two');
    	}
    	if($request->getMethod() == 'POST'){
    		if($nick && $pwd && $newPwd){
    			if (!preg_match("/^[\x{4e00}-\x{9fa5}a-zA-Z0-9_]{2,20}$/u",$nick)){
    				$code = $this->container->getParameter('init_one');
    			}else{
    				$user_nick = $em->getRepository('JiliApiBundle:User')->findByNick($nick);
    				if($user_nick){
    					$code = $this->container->getParameter('init_two');
    				}else{
    					if(!preg_match("/^[0-9A-Za-z_]{6,20}$/",$pwd)){
    						$code = $this->container->getParameter('init_three');
    					}else{
    						if($pwd == $newPwd){
    							if($is_user){
    								$is_from_wenwen[0]->setNick($nick);
    								$is_from_wenwen[0]->setPwd($pwd);
    								$em->persist($is_from_wenwen[0]);
    								$em->flush();
    								$id = $is_from_wenwen[0]->getId();
    							}else{
    								$user = new User();
    								$user->setNick($nick);
    								$user->setPwd($pwd);
    								$user->setEmail($u_email);
    								$user->setIsFromWenwen($this->container->getParameter('init_one'));
    								$user->setPoints($this->container->getParameter('init'));
    								$user->setIsInfoSet($this->container->getParameter('init'));
    								$em->persist($user);
    								$em->flush();
    								$id = $user->getId();
    							}
    							$request->getSession()->remove('email');
    							$request->getSession()->set('uid',$id);
    							$request->getSession()->set('nick',$nick);
    							return $this->redirect($this->generateUrl('_default_index'));
    						}else{
    							$code = $this->container->getParameter('init_four');
    						}
    					}
    				}
    			}
    		}else{
    			$code = $this->container->getParameter('init_five');
    		}
    	}
    	return $this->render('JiliApiBundle:Default:landing.html.twig',
    			array(
    				  'code'=>$code,
    				  'is_user'=>$is_user,
    				  'nick'=>$nick,
    				  'fornick'=>$fornick
    				 ));
    }
    
    /**
     * @Route("/about", name="_default_about")
     */
    public function aboutAction()
    {
    	return $this->render('JiliApiBundle:Default:about.html.twig');
    }
    
    /**
     * @Route("/error", name="_default_error")
     */
    public function errorAction()
    {
    	return $this->render('JiliApiBundle::error.html.twig');
    }
    

    /**
     * @Route("/services", name="_default_services")
     */
    public function servicesAction()
    {
    	return $this->render('JiliApiBundle::onservice.html.twig');
    }
    
    /**
     * @Route("/help", name="_default_help")
     */
    public function helpAction()
    {
    	return $this->render('JiliApiBundle:Default:help.html.twig');
    }
    
    
    /**
     * @Route("/service", name="_default_service")
     */
    public function serviceAction()
    {
    	return $this->render('JiliApiBundle:Default:service.html.twig');
    }
     
   
    
}
