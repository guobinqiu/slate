<?php
namespace Jili\ApiBundle\Controller;
use Jili\ApiBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Cookie;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GameController extends Controller
{
	
	/**
     * @Route("/index", name="_game_index")
     */
    public function indexAction(){  
        return $this->render('JiliApiBundle:Game:index.html.twig');
    }

    /**
     * @Route("/chick", name="_game_chick")
     */
    public function chickAction(){
        // $code = '';
        // $arr['code'] = $code;
        // if($this->checkMobile()=='noaceess'){
        //     $arr['code'] = $this->container->getParameter('init_one');
        // }
        // $em = $this->getDoctrine()->getManager();
        // $uid = '';
        // $uid = $this->get('request')->getSession()->get('uid');
        // if(!$uid){
        //    return $this->redirect($this->generateUrl('_user_login'));
        // }
        // $user = $em->getRepository('JiliApiBundle:User')->find($uid);

        // $key = sha1(date("Ymd")."ADF93768CF".$uid);
        // $url = "http://sugoroku01.cn.pag-asia.com/index.php?point_uid=".$uid."&nickname=".$user->getNick()."&key=".$key;
        // $arr['url'] = $url;
        // return $this->render('JiliApiBundle:Game:chick.html.twig',$arr);
        return $this->render('JiliApiBundle:Game:server.html.twig');
    }

      
    private function checkMobile(){
        $agent = strtolower($_SERVER['HTTP_USER_AGENT']);   
        $is_iphone = (strpos($agent, 'iphone')) ? true : false;
        $is_ipad = (strpos($agent, 'ipad')) ? true : false;   
        $is_android = (strpos($agent, 'android')) ? true : false; 
        if($is_iphone || $is_ipad || $is_android)
            return 'noaceess';
    }
    
}
