<?php
namespace Jili\ApiBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Jili\ApiBundle\Entity\PointsExchange;
use Jili\ApiBundle\Entity\SendMessage00;
use Jili\ApiBundle\Entity\SendMessage01;
use Jili\ApiBundle\Entity\SendMessage02;
use Jili\ApiBundle\Entity\SendMessage03;
use Jili\ApiBundle\Entity\SendMessage04;
use Jili\ApiBundle\Entity\SendMessage05;
use Jili\ApiBundle\Entity\SendMessage06;
use Jili\ApiBundle\Entity\SendMessage07;
use Jili\ApiBundle\Entity\SendMessage08;
use Jili\ApiBundle\Entity\SendMessage09;
use Jili\ApiBundle\Entity\IdentityConfirm;
use Jili\ApiBundle\Entity\ExchangeDanger;

class  ExchangeController extends Controller
{
    /**
     * @Route("/center", name="_exchange_index")
     */
    public function indexAction()
    {
        $token_key = $this->getTokenKey();
        return $this->render('JiliApiBundle:Exchange:index.html.twig',array('key'=>$token_key));
    }

    /**
     * @Route("/alipayInfo", name="_exchange_alipayInfo")
     */
    public function alipayInfoAction(){
        if(!$this->get('request')->getSession()->get('uid')){
            return $this->redirect($this->generateUrl('_user_login'));
        }else{
            $tokenKey = '';
            $getToken = '';
            $arr['code'] = '';
            $arr['existAlipay'] = '';
            $arr['existRealName'] = '';
            $arr['alipay'] = '';
            $pointschange  = new PointsExchange();
            $id = $this->get('request')->getSession()->get('uid');
            $em = $this->getDoctrine()->getManager();
            $request = $this->get('request');
            $getToken = $request->query->get('token');
            $tokenKey = $request->request->get('tokenKey');
            $arr['tokenKey'] = $tokenKey;
            if(!$getToken){
                $session = $this->getRequest()->getSession();
                $session->set('alipayToken', $tokenKey);
            }
            if(!$this->get('request')->getSession()->get('alipayToken')){
                return $this->redirect($this->generateUrl('_default_error'));
            }
            if($getToken){
                if($getToken != $this->get('request')->getSession()->get('alipayToken')){
                    return $this->redirect($this->generateUrl('_default_error'));
                }
            }else{
                if($tokenKey != $this->get('request')->getSession()->get('alipayToken')){
                    return $this->redirect($this->generateUrl('_default_error'));
                }
            }

            $user = $em->getRepository('JiliApiBundle:User')->find($id);
            $points = $user->getPoints();
            $arr['user'] = $user;
            $arr['points'] = $points;
            $rechange =  $request->request->get('rechange');
            $change_point =  $request->request->get('point');
            $alipay = $request->request->get('alipay');
            $re_alipay = $request->request->get('re_alipay');
            $real_name = $request->request->get('real_name');
            $existAlipay = $request->request->get('existAlipay');
            $existRealName = $request->request->get('existRealName'); 
            $targetAcc = $em->getRepository('JiliApiBundle:PointsExchange')->getTargetAccount($id,$this->container->getParameter('init_three'));
            if(!empty($targetAcc)){
                 $arr['existAlipay'] = $targetAcc[0]['targetAccount'];
                 $arr['existRealName'] = $targetAcc[0]['realName'];
            }
            if ($request->getMethod() == 'POST') {
                    $arr['alipay'] = $alipay;
                    if($rechange-$points>0){
                        $code = $this->container->getParameter('exchange_wr_point');
                        $arr['code'] = $code;
                    }else{
                        if($rechange == 3000 || $rechange == 5000){
                            if($existAlipay || $arr['existAlipay']==''){
                                if($alipay){
                                    if (preg_match("/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i",$alipay) || preg_match("/^13[0-9]{1}[0-9]{8}$|14[0-9]{1}[0-9]{8}$|15[0-9]{1}[0-9]{8}$|18[0-9]{1}[0-9]{8}$/",$alipay)){
                                        if($alipay == $re_alipay){
                                            if($real_name){
                                                // if(!eregi("[^\x80-\xff]",$real_name)){
                                                    $user->setPoints($points - intval($change_point));
                                                    $em->persist($user);
                                                    $em->flush();
                                                    $pointschange->setUserId($id);
                                                    $pointschange->setType($this->container->getParameter('init_three'));
                                                    $pointschange->setSourcePoint($points-intval($change_point));
                                                    $pointschange->setTargetPoint(intval($change_point));
                                                    $pointschange->setTargetAccount($alipay);
                                                    $pointschange->setRealName($real_name);
                                                    $pointschange->setExchangeItemNumber($change_point/100);
                                                    $pointschange->setIp($this->get('request')->getClientIp());
                                                    $em->persist($pointschange);
                                                    $em->flush();
                                                    $this->identDanger($this->container->getParameter('init_three'),$pointschange->getId(),$id);
                                                    $this->ipDanger($pointschange->getIp(),$pointschange->getId(),$id);
                                                    $this->mobileAlipayDanger($pointschange->getTargetAccount(),$pointschange->getId(),$id);
                                                    $this->pwdDanger($user->getPwd(),$pointschange->getId(),$id);
                                                    $token_key = $this->getTokenKey();
                                                    $session = $this->getRequest()->getSession();
                                                    $session->set('alipay', $token_key);
                                                    return $this->redirect($this->generateUrl('_exchange_finish',array('type'=>'alipay')));
                                                        
                                                // }else{
                                                //     $code = $this->container->getParameter('exchange_right_name');
                                                //     $arr['code'] = $code;        
                                                // }
                                            }else{
                                                $code = $this->container->getParameter('exchange_real_name');
                                                $arr['code'] = $code;
                                            }
                                        }else{
                                            $code = $this->container->getParameter('exchange_unsame_alipay');
                                            $arr['code'] = $code;
                                        }
                                    }else{
                                        $code = $this->container->getParameter('exchange_wr_alipay');
                                        $arr['code'] = $code;   
                                        
                                    }
                                }else{
                                    $code = $this->container->getParameter('exchange_en_alipay');
                                    $arr['code'] = $code;

                                }
                            }else{
                                $user->setPoints($points - intval($change_point));
                                $em->persist($user);
                                $em->flush();
                                $pointschange->setUserId($id);
                                $pointschange->setType($this->container->getParameter('init_three'));
                                $pointschange->setSourcePoint($points-intval($change_point));
                                $pointschange->setTargetPoint(intval($change_point));
                                $pointschange->setTargetAccount($arr['existAlipay']);
                                $pointschange->setRealName($arr['existRealName']);
                                $pointschange->setExchangeItemNumber($change_point/100);
                                $pointschange->setIp($this->get('request')->getClientIp());
                                $em->persist($pointschange);
                                $em->flush();
                                $this->identDanger($this->container->getParameter('init_three'),$pointschange->getId(),$id);
                                $this->ipDanger($pointschange->getIp(),$pointschange->getId(),$id);
                                $this->mobileAlipayDanger($pointschange->getTargetAccount(),$pointschange->getId(),$id);
                                $this->pwdDanger($user->getPwd(),$pointschange->getId(),$id);
                                $token_key = $this->getTokenKey();
                                $session = $this->getRequest()->getSession();
                                $session->set('alipay', $token_key);
                                return $this->redirect($this->generateUrl('_exchange_finish',array('type'=>'alipay')));
                            }          
                                
                        }else{
                            $code = $this->container->getParameter('exchange_wr_point');
                            $arr['code'] = $code;   
                            
                        }
                    }
                } 

        }
        $this->get('request')->getSession()->remove('alipayToken');
        return $this->render('JiliApiBundle:Exchange:alipayInfo.html.twig',$arr);
    }

    /**
     * @Route("/mobileInfo", name="_exchange_mobileInfo")
     */
    public function mobileInfoAction(){
        if(!$this->get('request')->getSession()->get('uid')){
            return $this->redirect($this->generateUrl('_user_login'));
        }else{
            $itemNumber = '';
            $tokenKey = '';
            $arr['mobile'] = '';
            $arr['code'] = '';
            $arr['existMobile'] = '';
            $pointschange  = new PointsExchange();
            $id = $this->get('request')->getSession()->get('uid');
            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository('JiliApiBundle:User')->find($id);
            $points = $user->getPoints();
            $arr['user'] = $user;
            $arr['points'] = $points;
            $request = $this->get('request');
            $rechange =  $request->request->get('rechange');
            $change_point =  $request->request->get('point');
            $mobile = $request->request->get('mobile');
            $re_mobile = $request->request->get('re_mobile');
            $existMobile = $request->request->get('existMobile');

            $tokenKey = $request->request->get('tokenKey');
            $session = $this->getRequest()->getSession();
            $session->set('mobileToken', $tokenKey);
            if(!$this->get('request')->getSession()->get('mobileToken')){
                return $this->redirect($this->generateUrl('_default_error'));
            }
            if($tokenKey != $this->get('request')->getSession()->get('mobileToken')){
                return $this->redirect($this->generateUrl('_default_error'));
            }
            $targetAcc = $em->getRepository('JiliApiBundle:PointsExchange')->getTargetAccount($id,$this->container->getParameter('init_four'));
            if(!empty($targetAcc)){
                 $arr['existMobile'] = $targetAcc[0]['targetAccount'];
            }
            if($request->getMethod() == 'POST') {
                $arr['mobile'] = $mobile;
                $arr['tokenKey'] = $tokenKey;
                if($rechange-$points>0){
                    $code = $this->container->getParameter('exchange_wr_point');
                    $arr['code'] = $code;
                }else{
                    if($rechange == 1015 || $rechange == 2010 || $rechange == 2995 || $rechange == 4960){
                        if($existMobile || $arr['existMobile']==''){
                            if($mobile){
                                    if (!preg_match("/^13[0-9]{1}[0-9]{8}$|14[0-9]{1}[0-9]{8}$|15[0-9]{1}[0-9]{8}$|18[0-9]{1}[0-9]{8}$/",$mobile)){
                                        $code = $this->container->getParameter('update_wr_mobile');
                                        $arr['code'] = $code;
                                    }else{
                                        if($mobile == $re_mobile){
                                            switch ($rechange) {
                                                 case '1015':
                                                    $itemNumber = 10;
                                                    break;
                                                 case '2010':
                                                    $itemNumber = 20;
                                                    break;
                                                 case '2995':
                                                    $itemNumber = 30;
                                                    break;
                                                 case '4960':
                                                    $itemNumber = 50;
                                                    break;
                                                 default:
                                                    $itemNumber = 10;
                                                    break;
                                            }
                                            $user->setPoints($points - intval($change_point));
                                            $em->persist($user);
                                            $em->flush();
                                            $pointschange->setUserId($id);
                                            $pointschange->setType($this->container->getParameter('init_four'));
                                            $pointschange->setSourcePoint($points-intval($change_point));
                                            $pointschange->setTargetPoint(intval($change_point));
                                            $pointschange->setTargetAccount($mobile);
                                            $pointschange->setExchangeItemNumber($itemNumber);
                                            $pointschange->setIp($this->get('request')->getClientIp());
                                            $em->persist($pointschange);
                                            $em->flush();               
                                            $this->ipDanger($pointschange->getIp(),$pointschange->getId(),$id);
                                            $this->mobileAlipayDanger($pointschange->getTargetAccount(),$pointschange->getId(),$id);

                                            $this->pwdDanger($user->getPwd(),$pointschange->getId(),$id);
                                            $token_key = $this->getTokenKey();
                                            $session = $this->getRequest()->getSession();
                                            $session->set('mobile', $token_key);

                                            return $this->redirect($this->generateUrl('_exchange_finish',array('type'=>'mobile')));
                                        }else{
                                            $code = $this->container->getParameter('exchange_unsame_mobile');
                                            $arr['code'] = $code;
                                        }
                                        
                                    }
                                }else{
                                    $code = $this->container->getParameter('exchange_en_mobile');
                                    $arr['code'] = $code;
                                }

                        }else{
                            switch ($rechange) {
                                 case '1015':
                                    $itemNumber = 10;
                                    break;
                                 case '2010':
                                    $itemNumber = 20;
                                    break;
                                 case '2995':
                                    $itemNumber = 30;
                                    break;
                                 case '4960':
                                    $itemNumber = 50;
                                    break;
                                 default:
                                    $itemNumber = 10;
                                    break;
                            }
                            $user->setPoints($points - intval($change_point));
                            $em->persist($user);
                            $em->flush();
                            $pointschange->setUserId($id);
                            $pointschange->setType($this->container->getParameter('init_four'));
                            $pointschange->setSourcePoint($points-intval($change_point));
                            $pointschange->setTargetPoint(intval($change_point));
                            $pointschange->setTargetAccount($arr['existMobile']);
                            $pointschange->setExchangeItemNumber($itemNumber);
                            $pointschange->setIp($this->get('request')->getClientIp());
                            $em->persist($pointschange);
                            $em->flush();
                            $this->ipDanger($pointschange->getIp(),$pointschange->getId(),$id);
                            $this->mobileAlipayDanger($pointschange->getTargetAccount(),$pointschange->getId(),$id);
                            $this->pwdDanger($user->getPwd(),$pointschange->getId(),$id);
                            $token_key = $this->getTokenKey();
                            $session = $this->getRequest()->getSession();
                            $session->set('mobile', $token_key);

                            return $this->redirect($this->generateUrl('_exchange_finish',array('type'=>'mobile')));

                        }
  
                    }else{
                        $code = $this->container->getParameter('exchange_wr_point');
                        $arr['code'] = $code;   
                        
                    }
                }        
                
            }
        }
        return $this->render('JiliApiBundle:Exchange:mobileInfo.html.twig',$arr);
    }

    /**
     * @Route("/amazonCheck", name="_exchange_amazonCheck")
     */
    public function amazonCheckAction(){
        if(!$this->get('request')->getSession()->get('uid')){
            return $this->redirect($this->generateUrl('_user_login'));
        }else{
            $request = $this->get('request');
            $tokenKey = $request->request->get('tokenKey');
            $session = $this->getRequest()->getSession();
            $session->set('amazonToken', $tokenKey);
            return $this->redirect($this->generateUrl('_exchange_amazonInfo'));
        }

    }

    /**
     * @Route("/alipayCheck", name="_exchange_alipayCheck")
     */
    public function alipayCheckAction(){
        if(!$this->get('request')->getSession()->get('uid')){
            return $this->redirect($this->generateUrl('_user_login'));
        }else{
            $request = $this->get('request');
            $tokenKey = $request->request->get('tokenKey');
            $session = $this->getRequest()->getSession();
            $session->set('alipayToken', $tokenKey);
            return $this->redirect($this->generateUrl('_exchange_identityCardComfirm',array('type'=>'alipay')));
        }

    }

    /**
     * @Route("/mobileCheck", name="_exchange_mobileCheck")
     */
    public function mobileCheckAction(){
        if(!$this->get('request')->getSession()->get('uid')){
            return $this->redirect($this->generateUrl('_user_login'));
        }else{
            $request = $this->get('request');
            $tokenKey = $request->request->get('tokenKey');
            $session = $this->getRequest()->getSession();
            $session->set('mobileToken', $tokenKey);
            return $this->redirect($this->generateUrl('_exchange_mobileInfo'));
        }

    }

     /**
     * @Route("/amazonInfo", name="_exchange_amazonInfo")
     */
    public function amazonInfoAction(){
        if(!$this->get('request')->getSession()->get('uid')){
            return $this->redirect($this->generateUrl('_user_login'));
        }else{
            $code = '';
            $tokenKey = '';
            $arr['code'] = $code;
            $pointschange  = new PointsExchange();
            $id = $this->get('request')->getSession()->get('uid');
            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository('JiliApiBundle:User')->find($id);
            $points = $user->getPoints();
            $arr['user'] = $user;
            $arr['points'] = $points;
            $request = $this->get('request');
            $rechange =  $request->request->get('rechange');
            $change_point =  $request->request->get('point');

            $tokenKey = $request->request->get('tokenKey');
            $session = $this->getRequest()->getSession();
            $session->set('amazonToken', $tokenKey);
            if(!$this->get('request')->getSession()->get('amazonToken')){
                return $this->redirect($this->generateUrl('_default_error'));
            }
            if($tokenKey != $this->get('request')->getSession()->get('amazonToken')){
                return $this->redirect($this->generateUrl('_default_error'));
            }
            if ($request->getMethod() == 'POST') {
                $arr['tokenKey'] = $tokenKey;
                if($rechange){
                    if($rechange > 0 && $rechange <= 5000){
                        if($rechange-$points>0){
                            $code = $this->container->getParameter('exchange_wr_point');
                            $arr['code'] = $code;
                        }else{
                            if($rechange%1000 != 0){
                                $code = $this->container->getParameter('exchange_wr_point');
                                $arr['code'] = $code;
                            }else{
                                $user->setPoints($points - intval($change_point * 1000));
                                $em->persist($user);
                                $em->flush();
                                $pointschange->setUserId($id);
                                $pointschange->setType($this->container->getParameter('init_two'));
                                $pointschange->setSourcePoint($points-intval($change_point*1000));
                                $pointschange->setTargetPoint(intval($change_point*1000));
                                $pointschange->setExchangeItemNumber($change_point);
                                $pointschange->setIp($this->get('request')->getClientIp());
                                $em->persist($pointschange);
                                $em->flush();
                                $this->ipDanger($pointschange->getIp(),$pointschange->getId(),$id);
                                $this->pwdDanger($user->getPwd(),$pointschange->getId(),$id);
                                $token_key = $this->getTokenKey();
                                $session = $this->getRequest()->getSession();
                                $session->set('amazon', $token_key);
                                return $this->redirect($this->generateUrl('_exchange_finish',array('type'=>'amazon')));
                            }
                        }
                    }else{
                        $code = $this->container->getParameter('exchange_wr_point');
                        $arr['code'] = $code;
                    }    
                }         
                
  
            }
        }
        return $this->render('JiliApiBundle:Exchange:amazonInfo.html.twig',$arr);

    }

    public function insertDanger($uid,$exid,$type,$dgcontent){
        $em = $this->getDoctrine()->getManager();
        $ExchangeDanger = new ExchangeDanger();
        $ExchangeDanger->setUserId($uid);
        $ExchangeDanger->setExchangeId($exid);
        $ExchangeDanger->setDangerType($type);
        $ExchangeDanger->setDangerContent($dgcontent);
        $em->persist($ExchangeDanger);
        $em->flush(); 
    }

    //判断身份证危险
    public function identDanger($type,$exchange_id,$uid){
        $em = $this->getDoctrine()->getManager();
        $identityCard = $em->getRepository('JiliApiBundle:IdentityConfirm')->findByUserId($uid);
        $existIdent = $em->getRepository('JiliApiBundle:IdentityConfirm')->issetIndentity($identityCard[0]->getIdentityCard(),$uid);
        if(!empty($existIdent)){
            foreach ($existIdent as $key => $value) {
                 $existUserExchange = $em->getRepository('JiliApiBundle:PointsExchange')->existOneExchange($value['userId'],$type);
                 if(!empty($existUserExchange)){
                    foreach ($existUserExchange as $key1 => $value1) {
                        $this->insertDanger($value1['userId'],$value1['id'],$this->container->getParameter('init_three'),$identityCard[0]->getIdentityCard());

                    }
                    $this->insertDanger($uid,$exchange_id,$this->container->getParameter('init_three'),$identityCard[0]->getIdentityCard());

                 }          

            }
           
        }

    }

    //判断手机,支付宝危险
    public function mobileAlipayDanger($targetAcc,$exchange_id,$uid){
        $em = $this->getDoctrine()->getManager();
        $existTar = $em->getRepository('JiliApiBundle:PointsExchange')->existTargetAcc($targetAcc,$uid);
        if(!empty($existTar)){
            foreach ($existTar as $key => $value) {
                $this->insertDanger($value['userId'],$value['id'],$this->container->getParameter('init_one'),$targetAcc);
            }
            $this->insertDanger($uid,$exchange_id,$this->container->getParameter('init_one'),$targetAcc);  
        }
     
    }

    //判断ip危险
    public function ipDanger($ip,$exchange_id,$uid){
        $em = $this->getDoctrine()->getManager();
        $existIp = $em->getRepository('JiliApiBundle:PointsExchange')->existIp($ip,$uid);
        if(!empty($existIp)){
            foreach ($existIp as $key => $value) {
                $this->insertDanger($value['userId'],$value['id'],$this->container->getParameter('init_two'),$ip);  
            } 
            $this->insertDanger($uid,$exchange_id,$this->container->getParameter('init_two'),$ip);  
        }

    }

    //判断密码危险
    public function pwdDanger($pwd,$exchange_id,$uid){
        $em = $this->getDoctrine()->getManager();
        $existPwd = $em->getRepository('JiliApiBundle:PointsExchange')->existPwd($pwd,$uid);
        if(!empty($existPwd)){
            foreach ($existPwd as $key => $value) { 
                $this->insertDanger($value['userId'],$value['id'],$this->container->getParameter('init_four'),$pwd);
            }
            $this->insertDanger($uid,$exchange_id,$this->container->getParameter('init_four'),$pwd);
        }

    }

    public function gotoComfirmUrl($type,$token){
        switch ($type) {
            case 'amazon':
                return $this->redirect($this->generateUrl('_exchange_amazonInfo'));
                break;
            case 'alipay':
                $session = $this->getRequest()->getSession();
                $session->set('alipayToken', $token);
                return $this->redirect($this->generateUrl('_exchange_alipayInfo',array('token'=>$token)));
                break;
            case 'mobile':
                return $this->redirect($this->generateUrl('_exchange_mobileInfo'));
                break;
            default:
                return $this->redirect($this->generateUrl('_default_error'));
                break;
        }
    }

    /**
     * @Route("/issetIdent", name="_exchange_issetIdent")
     */
    public function issetIdentAction(){
        $code = '';
        $id = $this->get('request')->getSession()->get('uid');
        $em = $this->getDoctrine()->getManager();
        $issetIdent = $em->getRepository('JiliApiBundle:IdentityConfirm')->findByUserId($id);
        if(!empty($issetIdent)){
            $code = $this->container->getParameter('init_one');
        }
        return new Response($code);
    }

    /**
     * @Route("/info/{type}", name="_exchange_identityCardComfirm")
     */
    public function IdentityCardComfirmAction($type){
        if(!$this->get('request')->getSession()->get('uid')){
            return $this->redirect($this->generateUrl('_user_login'));
        }else{
            $arr['code'] = '';
            $tokenKey = '';
            $arr['type'] = $type;
            $id = $this->get('request')->getSession()->get('uid');
            $request = $this->get('request');
            $identityCard =  $request->request->get('identityCard');

            $tokenKey = $request->request->get('tokenKey');
            $arr['tokenKey'] = $tokenKey;
            $session = $this->getRequest()->getSession();
            $session->set('alipayToken', $tokenKey);
            if(!$this->get('request')->getSession()->get('alipayToken')){
                return $this->redirect($this->generateUrl('_default_error'));
            }
            if($tokenKey != $this->get('request')->getSession()->get('alipayToken')){
                return $this->redirect($this->generateUrl('_default_error'));
            }
            $em = $this->getDoctrine()->getManager();
            // $issetIdent = $em->getRepository('JiliApiBundle:IdentityConfirm')->findByUserId($id);
            // if(!empty($issetIdent)){
            //     return $this->gotoComfirmUrl($type);
            // }
            $user = $em->getRepository('JiliApiBundle:User')->find($id);
            $arr['user'] = $user;
            if ($request->getMethod() == 'POST') {
                $btn = $request->request->get('btn');
                if(!$btn){
                    if($identityCard){
                        if(!$this->isValid($identityCard)){
                            $arr['code'] =  $this->container->getParameter('card_number_is_error');
                        }else{
                            $identC = new IdentityConfirm();
                            $identC->setUserId($id);
                            $identC->setIdentityCard($identityCard);
                            $em->persist($identC);
                            $em->flush(); 
                            return $this->gotoComfirmUrl($type,$tokenKey);
                        }
                    }else{
                        $arr['code'] =  $this->container->getParameter('card_number_is_null');
                    }
                }
            }
            
        }
        return $this->render('JiliApiBundle:Exchange:indentityConfirm.html.twig',$arr);

    }
    
    /**
     * @Route("/info", name="_exchange_info")
     */
    public function infoAction(){
        exit('不支持该兑换');
        if(!$this->get('request')->getSession()->get('uid')){
            return $this->redirect($this->generateUrl('_user_login'));
        }else{
            $code = '';
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
                        $code = $this->container->getParameter('exchange_wr_point');
                        $arr['code'] = $code;
                    }else{
                        if($rechange%500 != 0){
                            $code = $this->container->getParameter('exchange_wr_point');
                            $arr['code'] = $code;
                        }else{
                            if($user->getWenwenUser()){
                                $user->setPoints($points - intval($change_point * 500));
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
                                $this->ipDanger($pointschange->getIp(),$pointschange->getId(),$id);
                                return $this->redirect($this->generateUrl('_exchange_finish',array('type'=>'jili')));
                            }else{
                                $userExchange = $em->getRepository('JiliApiBundle:PointsExchange')->existUserExchange($id);
                                if(empty($userExchange)){
                                    if($wenwen){
                                        if (!preg_match("/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i",$wenwen)){
                                            $code = $this->container->getParameter('exchange_wr_mail');
                                            $arr['code'] = $code;
                                        }else{
                                            $user->setWenwenUser($wenwen);
                                            $user->setPoints($points - intval($change_point * 500));
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
                                            $this->ipDanger($pointschange->getIp(),$pointschange->getId(),$id);
                                            return $this->redirect($this->generateUrl('_exchange_finish',array('type'=>'jili')));
                                        }
                                    }else{
                                        $code = $this->container->getParameter('exchange_en_wen');
                                        $arr['code'] = $code;
                                    }
                                }else{
                                    $code = $this->container->getParameter('exchange_su_bind');
                                    $arr['code'] = $code;
                                }
                            }

                        }
                    }
                }else{
                    $code = $this->container->getParameter('exchange_wr_point');
                    $arr['code'] = $code;
                }             
                
            }
        }
        return $this->render('JiliApiBundle:Exchange:info.html.twig',$arr);
    }

    public function checkFinish($type){
        $code = '';
        switch ($type) {
            case 'amazon':
                if(!$this->get('request')->getSession()->get('amazon')){
                   $code = $this->container->getParameter('init_one');
                }
                break;
            case 'alipay':
                if(!$this->get('request')->getSession()->get('alipay')){
                   $code = $this->container->getParameter('init_one');
                }
                break;
            case 'mobile':
                 if(!$this->get('request')->getSession()->get('mobile')){
                    $code = $this->container->getParameter('init_one');
                }
                break;
            default:
                break;
        }
        return $code;

    }
    
    /**
     * @Route("/finish/{type}", name="_exchange_finish")
     */
    public function finishAction($type){
        if(!$this->get('request')->getSession()->get('uid')){
            return $this->redirect($this->generateUrl('_user_login'));
        }else{
            $em = $this->getDoctrine()->getManager();
            $id = $this->get('request')->getSession()->get('uid');
            $code = $this->checkFinish($type);
            if($code){
                return $this->redirect($this->generateUrl('_default_error'));
            }
            $user = $em->getRepository('JiliApiBundle:User')->find($id);
            $this->exchange_send_message($type,$id);
            if($type =='amazon'){
                $this->get('request')->getSession()->remove('amazonToken');
                $this->get('request')->getSession()->remove('amazon');
            }
            if($type =='alipay'){
                $this->get('request')->getSession()->remove('alipayToken');
                $this->get('request')->getSession()->remove('alipay');
            }
            if($type =='mobile'){
                $this->get('request')->getSession()->remove('mobileToken');
                $this->get('request')->getSession()->remove('mobile');
            }
            $arr['user'] = $user;
            $arr['type'] = $type;
            return $this->render('JiliApiBundle:Exchange:finish.html.twig',$arr);
        }
        
    }


    public function exchange_send_message($type,$uid){
      $title = '';
      $content = '';
      switch ($type) {
          case 'amazon':
              $title = $this->container->getParameter('exchange_ing_amazon_tilte');
              $content = $this->container->getParameter('exchange_ing_amazon_content');
              break;
          case 'alipay':
              $title = $this->container->getParameter('exchange_ing_alipay_tilte');
              $content = $this->container->getParameter('exchange_ing_alipay_content');
              break;
          case 'mobile':
              $title = $this->container->getParameter('exchange_ing_mobile_tilte');
              $content = $this->container->getParameter('exchange_ing_mobile_content');
              break;
          default:
              break;
      }
      if($title && $content){
          $parms = array(
                  'userid' => $uid,
                  'title' => $title,
                  'content' => $content
                );
          // $mycontrollerInstance = new AdminController();
          // $result = $mycontrollerInstance->insertSendMs($parms);
          $this->insertSendMs($parms);
      }
      
    }

    private function insertSendMs($parms=array()){
      extract($parms);
      if(strlen($userid)>1){
            $uid = substr($userid,-1,1);
      }else{
            $uid = $userid;
      }
      switch($uid){
            case 0:
                  $sm = new SendMessage00();
                  break;
            case 1:
                  $sm = new SendMessage01();
                  break;
            case 2:
                  $sm = new SendMessage02();
                  break;
            case 3:
                  $sm = new SendMessage03();
                  break;
            case 4:
                  $sm = new SendMessage04();
                  break;
            case 5:
                  $sm = new SendMessage05();
                  break;
            case 6:
                  $sm = new SendMessage06();
                  break;
            case 7:
                  $sm = new SendMessage07();
                  break;
            case 8:
                  $sm = new SendMessage08();
                  break;
            case 9:
                  $sm = new SendMessage09();
                  break;
      }
      $em = $this->getDoctrine()->getManager();
      $sm->setSendFrom($this->container->getParameter('init'));
      $sm->setSendTo($userid);
      $sm->setTitle($title);
      $sm->setContent($content);
      $sm->setReadFlag($this->container->getParameter('init'));
      $sm->setDeleteFlag($this->container->getParameter('init'));
      $em->persist($sm);
      $em->flush();
    }

    

    
    /**
     * @Route("/success", name="_exchange_success")
     */
    public function successAction(){
        
    }

    public function getTokenKey(){
        $key = '';
        $date = date("YmdHis");
        $request = $this->get('request');
        $uid = $request->getSession()->get('uid');
        if($uid){
            for ($i = 1; $i <= 9; $i++)
                $key .= sha1($uid.$date);
        }
        return sha1($key);
    }

    public static $areas = array(
        11 => "北京", 12 => "天津", 13 => "河北", 14 => "山西", 15 => "内蒙古",
        21 => "辽宁", 22 => "吉林", 23 => "黑龙江",
        31 => "上海", 32 => "江苏", 33 => "浙江", 34 => "安徽", 35 => "福建", 36 => "江西", 37 => "山东",
        41 => "河南", 42 => "湖北", 43 => "湖南", 44 => "广东", 45 => "广西", 46 => "海南",
        50 => "重庆", 51 => "四川", 52 => "贵州", 53 => "云南", 54 => "西藏",
        61 => "陕西", 62 => "甘肃", 63 => "青海", 64 => "宁夏", 65 => "新疆",
        71 => "台湾",
        81 => "香港", 82 => "澳门",
        91 => "国外",
    );
    public static $check_digits = '10X98765432';
    
    public function isValid($identityCard) {
        if(!self::lengthIsValid($identityCard)) {
            return false;
        }

        if(!self::regionIsValid($identityCard)) {
            return false;
        }

        if(!self::birthdayIsValid($identityCard)) {
            return false;
        }

        if(!self::checkDigitIsValid($identityCard)) {
            return false;
        }

        return true;
    }

    public static function lengthIsValid($identityCard) {
        if (strlen($identityCard) === 15) {
            return true;
        }

        if(strlen($identityCard) === 18) {
            return true;
        }

        return false;
    }

    public static function regionIsValid($identityCard) {
        $region_id = self::getRegion($identityCard);

        return array_key_exists($region_id, self::$areas);
    }

    public static function birthdayIsValid($identityCard) {
        $birthday = self::getBirthDay($identityCard);
        return checkdate($birthday['month'], $birthday['day'], $birthday['year']);
    }

    public static function getRegion($identityCard) {
        return (int) mb_substr($identityCard, 0, 2);
    }

    public static function checkDigitIsValid($identityCard) {
        # check digit doesn't exists
        if(strlen($identityCard) !== 18) {
            return true;
        }
        $calclated = self::calcCheckDigit($identityCard);
        $check_digit = $identityCard[17];

        return ($calclated == $check_digit);
    }

    /**
     * only for length = 18
     */
    public static function calcCheckDigit($identityCard) {
        if(strlen($identityCard) !== 18) {
            return null;
        }

        $digits = array();
        for($i = 0; $i < mb_strlen($identityCard); $i++) {
            $digits[] = (int) $identityCard[$i];
        }
        $calc = ($digits[0] + $digits[10]) * 7
                + ($digits[1] + $digits[11]) * 9
                + ($digits[2] + $digits[12]) * 10
                + ($digits[3] + $digits[13]) * 5
                + ($digits[4] + $digits[14]) * 8
                + ($digits[5] + $digits[15]) * 4
                + ($digits[6] + $digits[16]) * 2
                + $digits[7] * 1
                + $digits[8] * 6
                + $digits[9] * 3
            ;
        $calc = $calc % 11;
        return self::$check_digits[$calc];

    }

    /**
     * 取得生日（由身份证号）
     * @param int $id 身份证号
     * @return string
     */
    private static function getBirthDay($identityCard) {
        switch (strlen ( $identityCard )) {
        case 15 :
            $year = "19" . substr ( $identityCard , 6, 2 );
            $month = substr ( $identityCard , 8, 2 );
            $day = substr ( $identityCard , 10, 2 );
            break;
        case 18 :
            $year = substr ( $identityCard , 6, 4 );
            $month = substr ( $identityCard , 10, 2 );
            $day = substr ( $identityCard , 12, 2 );
            break;
        }
        $birthday = array ('year' => $year, 'month' => $month, 'day' => $day );
        return $birthday;
    }
     
   
    
}
