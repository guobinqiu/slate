<?php
namespace Jili\ApiBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Jili\ApiBundle\Entity\User;
use Jili\ApiBundle\Entity\LoginLog;
use Symfony\Component\HttpFoundation\Request;
use Jili\ApiBundle\Mailer;
use Jili\ApiBundle\Form\RegType;

class DefaultController extends Controller
{
	/**
	 * @Route("/", name="_default")
	 */
    public function indexAction()
    {
    	$em = $this->getDoctrine()->getManager();
        $sql = 'select ad.type,ad.position,a.title,a.content from ad_position ad inner join advertiserment a on ad.ad_id = a.id';
        
        $advertise = $em->getConnection()->executeQuery($sql)->fetchAll();
    	foreach ($advertise as $k=>$v){
    		if($v['type']==0){
    			$arr['advertise_banner'][] = $v;
    		}
    		if($v['type']==1){
    			$arr['advertise'][] = $v;
    		}
    	}   
        return $this->render('JiliApiBundle:Default:index.html.twig',$arr);
    }
    
    /**
     * @Route("/login", name="_default_login")
     */
    public function loginAction(){
//     	$user = new User();
//     	$form = $this->createForm(new LoginType() , $user);
//     	$form->bind($request);
    	
    	$nick = 'test';
//     	$request = $this->get('request');
//     	$nick = $request->getParameter("nick");
//     	$pwd = $request->getParameter("pwd");
    	$pwd = $this->pw_encode('123456');
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

    	return $this->render('JiliApiBundle:Default:login.html.twig',array(
    			'form' => $form->createView(),));
    }
    
    /**
     * @Route("/reg", name="_default_reg")
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
    	
//     	$user = new User();
//     	$user->setNick('test');
//     	$user->setPwd($this->pw_encode('1234567'));
//     	$user->setSex(1);
//     	$user->setBirthday('2013-01-01 00:00:00');
//     	$user->setEmail('aasfa');
//     	$user->setIsEmailConfirmed('1');
//     	$user->setTel('12143124');
//     	$user->setIsTelConfirmed('1');
//     	$user->setCity('1');
//     	$user->setIdentityNum('11');
//     	$user->setRegisterDate('2013-01-01 00:00:00');
//     	$user->setLastlogindate('2013-01-01 00:00:00');
//     	$user->setLastloginip('192.168.1.1');
//     	$user->setPoints('22');

    	return $this->render('JiliApiBundle:Default:reg.html.twig',array(
            'form' => $form->createView(),));
    }
    
    private function pw_encode($password)
    {
    	$seed = '';
    	for ($i = 1; $i <= 9; $i++)
    		$seed .= sha1($password.'0123456789abcdef');
    	for ($i = 1; $i <= 11; $i++)
    		$seed .= sha1($seed);
    	return sha1($seed);
    }
    

    /**
     * @Route("/mission", name="_default_mission")
     */
    public function missionAction(){
    	
    	$nick = 'test';
    	$email = '278583642@qq.com';
    	if($this->sendMail($nick, $email)){
    		
    	}
    	return $this->render('JiliApiBundle:Default:mission.html.twig');
    }
    
    private function sendMail($nick,$email){
    	$message = \Swift_Message::newInstance()
        ->setSubject('find pwd')
        ->setFrom('quickresearch_1@163.com')
        ->setTo($email)
        ->setBody(
            $this->renderView(
                'JiliApiBundle:Default:email.txt.twig',array('name' => $nick)
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
