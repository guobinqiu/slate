<?php
namespace Jili\ApiBundle\Controller;
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
	 * @Route("/", name="_default_index")
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
		$repository = $em->getRepository('JiliApiBundle:Advertiserment');
		$advertise = $repository->getAdvertiserment();
		$arr['advertise'] = $repository->getAdvertiserList();
		$callboard = $em->getRepository('JiliApiBundle:Callboard')->findAll();
		$arr['callboard'] =  $callboard;
    	foreach ($advertise as $k=>$v){
    		$i = 0;
    		if($v['type']==0){
    			$arr['advertise_banner'][] = $v;
    		}
    	} 
    	  
        return $this->render('JiliApiBundle:Default:index.html.twig',$arr);
    }
    
    
    /**
     * @Route("/fastLogin", name="_default_fastLogin")
     */
    function fastLoginAction(){
    	$arr['userInfo'] = array();
    	$request = $this->get('request');
    	$email = $request->request->get('email');
    	$pwd = $request->request->get('pwd');
    	$em_email = $this->getDoctrine()
    	->getRepository('JiliApiBundle:User')
    	->findByEmail($email);
    	$request = $this->get('request');
    	if ($request->getMethod() == 'POST'){
			if(!$em_email){
				echo 'email is unexist!';
			}else{
				$id = $em_email[0]->getId();
				$em = $this->getDoctrine()->getEntityManager();
				$user = $em->getRepository('JiliApiBundle:User')->find($id);
				if($user->pw_encode($pwd) != $user->getPwd()){
					echo 'pwd is error!';
				}else{
					$session = new Session();
					$session->start();
					if($request->request->get('remember_me')=='1'){
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
// 					$userInfo = $em->getRepository('JiliApiBundle:User')->find($session->get('uid'));
// 					$arr['userInfo'] = $userInfo;
				}
			}
    	}
    	return $this->redirect($this->generateUrl('_default_index'));
//     	return $this->render('JiliApiBundle:Default:index.html.twig',$arr);
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
     * @Route("/help", name="_default_help")
     */
    public function helpAction()
    {
    	return $this->render('JiliApiBundle:Default:help.html.twig');
    }
     
   
    
}
