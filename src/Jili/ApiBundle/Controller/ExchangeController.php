<?php
namespace Jili\ApiBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Jili\ApiBundle\Entity\PointsExchange;

class  ExchangeController extends Controller
{
	/**
	 * @Route("/center", name="_exchange_index")
	 */
    public function indexAction()
    {
        return $this->render('JiliApiBundle:Exchange:index.html.twig');
    }
    
    /**
     * @Route("/info", name="_exchange_info")
     */
    public function infoAction(){
    	if(!$this->get('request')->getSession()->get('uid')){
    		return $this->redirect($this->generateUrl('_user_login'));
    	}else{
    		$code = $this->container->getParameter('init');
    		$arr['code'] = $code;
    		$pointschange  = new PointsExchange();
    		$id = $this->get('request')->getSession()->get('uid');
    		$em = $this->getDoctrine()->getManager();
    		$user = $em->getRepository('JiliApiBundle:User')->find($id);
    		$pointsExchange = $em->getRepository('JiliApiBundle:PointsExchange')->getExchangeStatus($id);
    		$points = $user->getPoints();
            $arr['user'] = $user;
            $arr['points'] = $points;
            $arr['pointsExchange'] = $pointsExchange;
            $request = $this->get('request');
            $wenwen =  $request->request->get('wenwen');
            $ck =  $request->request->get('ck');
            $arr['ck'] = $ck;
            $change_point =  $request->request->get('point');
            if ($request->getMethod() == 'POST') {
            	if($wenwen){
            		if (!preg_match("/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i",$wenwen)){
            			$code = $this->container->getParameter('init_two');
            			$arr['code'] = $code;
            		}else{
            			if(empty($pointsExchange)){
            				$user->setWenwenUser($wenwen);
            				$em->persist($user);
            			}
            			$pointschange->setUserId($id);
            			$pointschange->setType($this->container->getParameter('init_one'));
            			//$pointschange->setTargetAccount();
            			$pointschange->setSourcePoint($points);
            			$pointschange->setTargetPoint(intval($change_point*500));
            			//$pointschange->setStatus();
            			$pointschange->setIp($this->get('request')->getClientIp());
            			$em->persist($pointschange);
            			$em->flush();
            			return $this->redirect($this->generateUrl('_exchange_finish'));
            		}
            	}else{
            		$code = $this->container->getParameter('init_one');
            		$arr['code'] = $code;
            	}
            }
    	}
    	return $this->render('JiliApiBundle:Exchange:info.html.twig',$arr);
    }
    
    /**
     * @Route("/finish", name="_exchange_finish")
     */
    public function finishAction(){
    	if(!$this->get('request')->getSession()->get('uid')){
    		return $this->redirect($this->generateUrl('_user_login'));
    	}else{
    		$em = $this->getDoctrine()->getManager();
    		$id = $this->get('request')->getSession()->get('uid');
    		$user = $em->getRepository('JiliApiBundle:User')->find($id);
    		$arr['user'] = $user;
    	}
    	return $this->render('JiliApiBundle:Exchange:finish.html.twig',$arr);
    }
    
    
    /**
     * @Route("/success", name="_exchange_success")
     */
    public function successAction(){
    	
    }
     
   
    
}
