<?php
namespace Jili\ApiBundle\Controller;
use Jili\ApiBundle\Entity\User;
use Jili\ApiBundle\Entity\PointsExchange;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Cookie;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Jili\ApiBundle\Mailer;
use Jili\ApiBundle\Form\RegType;
use Jili\ApiBundle\Entity\LoginLog;
use Jili\ApiBundle\Entity\WenwenUser;
use Jili\ApiBundle\Entity\CallBoard;
use Jili\ApiBundle\Entity\UserGameVisit;

class DefaultController extends Controller
{
	
	/**
	 * @Route("/", name="_default_index",requirements={"_scheme"="https"})
	 * 
	 */ 
    public function indexAction()
    {
        if($_SERVER['HTTP_HOST']=='91jili.com')
            return $this->redirect('https://www.91jili.com');
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
        $code = '';
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
            	// $code = $this->container->getParameter('init_one');
                $code = $this->container->getParameter('login_wr');
            }else{
            	$id = $em_email[0]->getId();
            	$em = $this->getDoctrine()->getEntityManager();
            	$user = $em->getRepository('JiliApiBundle:User')->find($id);
            	if($user->pw_encode($pwd) != $user->getPwd()){
            		// 					echo 'pwd is error!';
            		// $code = $this->container->getParameter('init_two');
                    $code = $this->container->getParameter('login_wr');
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
		$advertiseBanner = $em->getRepository('JiliApiBundle:AdBanner')->getInfoBanner();
		$advertise = $repository->getAdvertiserList();
		$callboard = $em->getRepository('JiliApiBundle:CallBoard')->getFiveCallboard();
        $exchangeInfo = $em->getRepository('JiliApiBundle:PointsExchange')->exList();
        foreach ($exchangeInfo as $key => $value) {
            if($this->countStrs($exchangeInfo[$key]['nick']) > 12){
                if($this->isUnion($exchangeInfo[$key]['nick']))
                    $exchangeInfo[$key]['nick'] = $this->my_substr($value['nick'], 0,15).'...';
                else{
                    if($this->conUnion($exchangeInfo[$key]['nick']))
                        $exchangeInfo[$key]['nick'] = $this->my_substr($value['nick'], 0,12).'...';
                    else
                        $exchangeInfo[$key]['nick'] = $this->my_substr($value['nick'], 0,10).'...';
                }
            }
        }
        $arr['banner_count'] = count($advertiseBanner);
        $arr['exchange'] = $exchangeInfo;
		$arr['callboard'] =  $callboard;
		$arr['advertise_banner'] = $advertiseBanner;
    	$arr['advertise'] = $advertise;
        return $this->render('JiliApiBundle:Default:index.html.twig',$arr);
    }

    public function conUnion($str){
        $pattern = '/[^\x00-\x80]/';
        if(preg_match($pattern,$str)){
            return true;// "含有中文";
        }else{
            return false;
        }
    }

    public function isUnion($str){
        if(!eregi("[^\x80-\xff]","$str")){
            return true;//全是中文
        }else{
            return false;
        } 
    }

    public function my_substr($str, $start, $len)
    {
        $tmpstr = "";
        $strlen = $start + $len;
        for($i = 0; $i < $strlen; $i++)
        {
            if( ord( substr($str, $i, 1) ) > 0xa0 )
            {
                $tmpstr .= substr($str, $i, 3);
                $i += 2;
            } else
                $tmpstr .= substr($str, $i, 1);
        }
        return $tmpstr;
    }

    public function  countStrs($str){
        $len=strlen($str); 
        $i=0;  
        while($i<$len)  
        {  
               if(preg_match("/^[".chr(0xa1)."-".chr(0xff)."]+$/",$str[$i]))  
               {  
                 $i+=2;  
               }  
               else  
               {  
                 $i+=1;  
               }  
        }  
        return $i;  
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

    public function getToken($email){
        $seed="ADF93768CF";
        $hash = sha1($email.$seed);
        for ($i = 0; $i < 5; $i++) { 
            $hash = sha1($hash); 
        }
        return $hash;
    }

    
     /**
     * @Route("/landing", name="_default_landing",requirements={"_scheme"="https"})
     */
    public function landingAction(){
        if($this->get('request')->getSession()->get('uid')){
            return $this->redirect($this->generateUrl('_default_index'));
        }
        $is_user = '';
        $code = '';
        $request = $this->get('request');
        $token = $request->query->get('secret_token');
        $nick = $request->request->get('nick');
        $pwd = $request->request->get('pwd');
        $newPwd = $request->request->get('newPwd');
        if($token){
            $request->getSession()->remove('token');
            $request->getSession()->set('token',$token);
        }
        $u_token = $request->getSession()->get('token');
        if(!$u_token){
            return $this->redirect($this->generateUrl('_user_reg'));
        }
        $em = $this->getDoctrine()->getManager();
        $wenuser = $em->getRepository('JiliApiBundle:WenwenUser')->findByToken($u_token);
        if(!$wenuser){
            $params = json_decode(base64_decode(strtr($u_token, '-_', '+/')));
            $email = ''; 
            $signature = ''; 
            $uniqkey = '';
            if($params){
                $email = $params->email;
                $signature = $params->signature;
                if(isset($params->uniqkey))
                    $uniqkey = $params->uniqkey;
            }   
            if($this->getToken($email) == $signature){ 
                $is_email = $em->getRepository('JiliApiBundle:User')->getWenwenUser($email);
                if($is_email){
                    $is_user = $this->container->getParameter('init_one');
                }else{
                    if($request->getMethod() == 'POST'){
                        if($nick && $pwd && $newPwd){
                            if (!preg_match("/^[\x{4e00}-\x{9fa5}a-zA-Z0-9_]{2,20}$/u",$nick)){
                                $code = $this->container->getParameter('init_one');
                            }else{
                                $user_nick = $em->getRepository('JiliApiBundle:User')->findNick($email,$nick);
                                if($user_nick)
                                    $code = $this->container->getParameter('init_two');
                                else{
                                    if(!preg_match("/^[0-9A-Za-z_]{6,20}$/",$pwd)){
                                        $code = $this->container->getParameter('init_three');
                                    }else{
                                        if($pwd == $newPwd){
                                             $isset_email = $em->getRepository('JiliApiBundle:User')->findByEmail($email);
                                            if($isset_email){
                                                $isset_email[0]->setNick($nick);
                                                $isset_email[0]->setPwd($pwd);
                                                $isset_email[0]->setIsFromWenwen($this->container->getParameter('init_one'));
                                                $isset_email[0]->setUniqkey($uniqkey);
                                                $em->persist($isset_email[0]);
                                                $em->flush();
                                                $id = $isset_email[0]->getId();
                                            }else{
                                                $user = new User();
                                                $user->setNick($nick);
                                                $user->setPwd($pwd);
                                                $user->setEmail($email);
                                                $user->setIsFromWenwen($this->container->getParameter('init_one'));
                                                $user->setPoints($this->container->getParameter('init'));
                                                $user->setIsInfoSet($this->container->getParameter('init'));
                                                $user->setUniqkey($uniqkey);
                                                $em->persist($user);
                                                $em->flush();
                                                $id = $user->getId();
                                            }
                                            $request->getSession()->remove('token');
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
                }  
            }else{
                return $this->redirect($this->generateUrl('_user_reg'));
            }   
        }else{
            $email = $wenuser[0]->getEmail();   
            $is_email = $em->getRepository('JiliApiBundle:User')->getWenwenUser($email);
            if($is_email){
                $is_user = $this->container->getParameter('init_one');
            }else{
                if($request->getMethod() == 'POST'){
                    if($nick && $pwd && $newPwd){
                        if (!preg_match("/^[\x{4e00}-\x{9fa5}a-zA-Z0-9_]{2,20}$/u",$nick)){
                            $code = $this->container->getParameter('init_one');
                        }else{
                            $user_nick = $em->getRepository('JiliApiBundle:User')->findNick($email,$nick);
                            if($user_nick)
                                $code = $this->container->getParameter('init_two');
                            else{
                                if(!preg_match("/^[0-9A-Za-z_]{6,20}$/",$pwd)){
                                    $code = $this->container->getParameter('init_three');
                                }else{
                                    if($pwd == $newPwd){
                                         $isset_email = $em->getRepository('JiliApiBundle:User')->findByEmail($email);
                                        if($isset_email){
                                            $isset_email[0]->setNick($nick);
                                            $isset_email[0]->setPwd($pwd);
                                            $isset_email[0]->setIsFromWenwen($this->container->getParameter('init_one'));
                                            $em->persist($isset_email[0]);
                                            $em->flush();
                                            $id = $isset_email[0]->getId();
                                        }else{
                                            $user = new User();
                                            $user->setNick($nick);
                                            $user->setPwd($pwd);
                                            $user->setEmail($email);
                                            $user->setIsFromWenwen($this->container->getParameter('init_one'));
                                            $user->setPoints($this->container->getParameter('init'));
                                            $user->setIsInfoSet($this->container->getParameter('init'));
                                            $em->persist($user);
                                            $em->flush();
                                            $id = $user->getId();
                                        }
                                        $request->getSession()->remove('token');
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
            }

        }
        return $this->render('JiliApiBundle:Default:landing.html.twig',
                array(
                      'code'=>$code,
                      'is_user'=>$is_user,
                      'nick'=>$nick,
                      'email'=>$email
                     ));

    }

    /**
     * @Route("/isExistVist", name="_default_isExistVist")
     */
    public function isExistVistAction()
    {
        $day = date('Ymd');
        $request = $this->get('request');
        $em = $this->getDoctrine()->getManager();
        $id = $request->getSession()->get('uid');
        if($id){
            $visit = $em->getRepository('JiliApiBundle:UserGameVisit')->getGameVisit($id,$day);
            if(empty($visit)){
                $code = $this->container->getParameter('init_one');
            }else{
                $code = $this->container->getParameter('init');
            }
        }else{
            $code = $this->container->getParameter('init');
        }
        return new Response($code);

    }

    /**
     * @Route("/gameVisit", name="_default_gameVisit")
     */
    public function gameVisitAction()
    {   
        $day = date('Ymd');
        $request = $this->get('request');
        $em = $this->getDoctrine()->getManager();
        $id = $request->getSession()->get('uid');
        if($id){
            $visit = $em->getRepository('JiliApiBundle:UserGameVisit')->getGameVisit($id,$day);
            if(empty($visit)){
                $gameVisit = new UserGameVisit();
                $gameVisit->setUserId($id);
                $gameVisit->setVisitDate($day);
                $em->persist($gameVisit);
                $em->flush();
            }
            $code =  $this->container->getParameter('init_one');
        }else{
            $code = $this->container->getParameter('init');
        }
        return new Response($code);

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