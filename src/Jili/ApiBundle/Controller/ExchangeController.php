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
    		$points = $user->getPoints();
            $arr['user'] = $user;
            $arr['points'] = $points;
            $request = $this->get('request');
            $wenwen =  $request->request->get('wenwen');
            $rechange =  $request->request->get('rechange');
            $ck =  $request->request->get('ck');
            $arr['ck'] = $ck;
            $change_point =  $request->request->get('point');
            if ($request->getMethod() == 'POST') {
                if($rechange > 0 && $rechange <= 5000){
                    if($rechange-$points>0){
                        $code = $this->container->getParameter('init_four');
                        $arr['code'] = $code;
                    }else{
                        if($rechange%500 != 0){
                            $code = $this->container->getParameter('init_four');
                            $arr['code'] = $code;
                        }else{
                            if($user->getWenwenUser()){
                                $user->setPoints($points-intval($change_point*500));
                                $em->persist($user);
                                $em->flush();
                                $pointschange->setUserId($id);
                                $pointschange->setType($this->container->getParameter('init_one'));
                                $pointschange->setTargetAccount($user->getWenwenUser());
                                $pointschange->setSourcePoint($points-intval($change_point*500));
                                $pointschange->setTargetPoint(intval($change_point*500));
                                //$pointschange->setStatus();
                                $pointschange->setIp($this->get('request')->getClientIp());
                                $em->persist($pointschange);
                                $em->flush();
                                return $this->redirect($this->generateUrl('_exchange_finish'));
                            }else{
                                $userExchange = $em->getRepository('JiliApiBundle:PointsExchange')->existUserExchange($id);
                                if(empty($userExchange)){
                                    if($wenwen){
                                        if (!preg_match("/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i",$wenwen)){
                                            $code = $this->container->getParameter('init_two');
                                            $arr['code'] = $code;
                                        }else{
                                            $user->setWenwenUser($wenwen);
                                            $user->setPoints($points-intval($change_point*500));
                                            $em->persist($user);
                                            $em->flush();
                                            $pointschange->setUserId($id);
                                            $pointschange->setType($this->container->getParameter('init_one'));
                                            $pointschange->setTargetAccount($wenwen);
                                            $pointschange->setSourcePoint($points-intval($change_point*500));
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
                                }else{
                                    $code = $this->container->getParameter('init_three');
                                    $arr['code'] = $code;
                                }
                            }

                        }
                    }
                }else{
                    $code = $this->container->getParameter('init_four');
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
