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
use Jili\ApiBundle\Entity\AmazonCoupon;
use Jili\ApiBundle\Entity\RegisterReward;
use Gregwar\Captcha\CaptchaBuilder;
use Jili\ApiBundle\Entity\SendCallboard;
use Jili\ApiBundle\Entity\IsReadCallboard;
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
	* @Route("/createFlag", name="_user_createFlag")
	*/
	public function createFlagAction(){	
		$code = '';
		$request = $this->get('request');
		$id = $request->getSession()->get('uid');
		$em = $this->getDoctrine()->getManager();
		$user = $em->getRepository('JiliApiBundle:User')->find($id);
		if($user->getDeleteFlag() == 1){
				$this->removeSession();
				$code = $this->container->getParameter('init_one');
		}
		if(!$request->getSession()->get('flag')){
			$session = $this->getRequest()->getSession();
            $session->set('flag', 1);
			$loginlog = new Loginlog();
			$loginlog->setUserId($id);
			$loginlog->setLoginDate(date_create(date('Y-m-d H:i:s')));
			$loginlog->setLoginIp($this->get('request')->getClientIp());
			$em->persist($loginlog);
			$em->flush();
			if($user->getDeleteFlag() == 1){
				$this->removeSession();
				// return $this->redirect($this->generateUrl('_default_index'));
				$code = $this->container->getParameter('init_one');
			}
		}
		return new Response($code);

	}

	public function removeSession(){
		$this->get('request')->getSession()->remove('uid');
		$this->get('request')->getSession()->remove('nick');
		setcookie ("jili_uid", "", time() - 3600,'/');
		setcookie ("jili_nick", "", time() - 3600,'/');
	}

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
	 * @Route("/getCity", name="_user_getCity")
	 */
	public function getCityAction()
	{
		$array = array();
		//$arr[] = array('id'=>0,'cityName'=>'请选择地区');
		$request = $this->get('request');
		$cid = $request->query->get('cid');
		$em = $this->getDoctrine()->getManager();
		$city = $em->getRepository('JiliApiBundle:CityList')->findByProvinceId($cid);
		if($city){
			foreach ($city as $key => $value){
				$arr[] = array('id'=>$value->getId(),'cityName'=>$value->getCityName());
			}
			return new Response(json_encode($arr));
		}else{
			return new Response('');
		}
		
	}

	/**
	 * @Route("/getCoupon", name="_user_getCoupon")
	 */
	public function getCouponAction(){
		$code = '';
		$getCoupon = '';
		$request = $this->get('request');
		$id = $request->getSession()->get('uid');
		if(!$id){
            return $this->redirect($this->generateUrl('_default_index'));
        }
        $em = $this->getDoctrine()->getManager();
       
        $userCount = $em->getRepository('JiliApiBundle:AmazonCoupon')->isUserCoupon($id);
        if($userCount > $this->container->getParameter('init')){
			$code = $this->container->getParameter('init_one');
			$userCoupon = $em->getRepository('JiliApiBundle:AmazonCoupon')->findByUserid($id);
			$getCoupon = $userCoupon[0]->getCoupon();
        }else{
        	$amazonCount = $em->getRepository('JiliApiBundle:AmazonCoupon')->countCoupon();
        	if($amazonCount > $this->container->getParameter('init')){
        		$amazon = $em->getRepository('JiliApiBundle:AmazonCoupon')->getAmcoupon();
	        	$getCoupon = $amazon['0']['coupon'];
		        $amazonCoupon = $em->getRepository('JiliApiBundle:AmazonCoupon')->find($amazon['0']['id']);
		        $amazonCoupon->setUserId($id);
		        $em->flush();
        	}else{
        		$code = $this->container->getParameter('init_two');
       		}	
        }
        return $this->render('JiliApiBundle:User:getCoupon.html.twig',array(
				'coupon'=>$getCoupon,
				'code'=>$code
				));
       
	}
    //领取亚马逊优惠券
	private function getCoupons($id){
		$em = $this->getDoctrine()->getManager();
		$isuserAmazon = $em->getRepository('JiliApiBundle:AmazonCoupon')->findByUserid($id);
		if(empty($isuserAmazon)){
			$amazon = $em->getRepository('JiliApiBundle:AmazonCoupon')->getAmcoupon();
			// $getCoupon = $amazon['0']['coupon'];
	        $amazonCoupon = $em->getRepository('JiliApiBundle:AmazonCoupon')->find($amazon['0']['id']);
	        $amazonCoupon->setUserId($id);
	        $em->flush();
	        $reward =  new RegisterReward();
			$reward->setUserId($id);
			$reward->setType($this->container->getParameter('init_one'));
			$em->persist($reward);
			$em->flush();
	        return true;
		}else{
			return false;
		}
		
	}
    // 是否完善资料
	private function isExistInfo($userid){
		$em = $this->getDoctrine()->getManager();
		$user = $em->getRepository('JiliApiBundle:User')->find($userid);
		if($user->getSex() && $user->getBirthday() && $user->getProvince() && $user->getCity() && $user->getIncome() && $user->getHobby())
			return true;
		else
			return false;
	}
	//是否给过奖励
	private function isGetReward($userid,$componType){
		$em = $this->getDoctrine()->getManager();
		$user = $em->getRepository('JiliApiBundle:RegisterReward')->findByUserid($userid);
		if($user){
			if($componType == 'point'){
				if($user[0]->getType() == $this->container->getParameter('init_one'))
					return $this->container->getParameter('init_four');//参加其他活动领取的
				if($user[0]->getType() == $this->container->getParameter('init_two'))
					return $this->container->getParameter('init_one');
			}else{
				if($user[0]->getType() == $this->container->getParameter('init_one')){
					$isuserAmazon = $em->getRepository('JiliApiBundle:AmazonCoupon')->findByUserid($userid);
					if(empty($isuserAmazon)){
						return $this->container->getParameter('init_three');//获得米粒的
					}else{
						return $this->container->getParameter('init_two');//获得优惠券的
					}
				}else{
					return $this->container->getParameter('init_four');//参加其他活动领取的
				}
			}
		}else{
			return false;
		}
		
	}

	//给米粒奖励
	private function getPointReward($componType,$userid){
		$em = $this->getDoctrine()->getManager();
		$isuserPoint = $em->getRepository('JiliApiBundle:RegisterReward')->findByUserid($userid);
		if(empty($isuserPoint)){
			$reward =  new RegisterReward();
			$reward->setUserId($userid);
			if($componType == 'point'){
				$reward->setType($this->container->getParameter('init_two'));
				$reward->setRewards($this->container->getParameter('init_fivty'));
				$em->persist($reward);
				$em->flush();
				$this->getPointHistory($userid,$this->container->getParameter('init_fivty'));
				$user = $em->getRepository('JiliApiBundle:User')->find($userid);
				$user->setPoints(intval($user->getPoints()+$this->container->getParameter('init_fivty')));
				$em->persist($user);
				$em->flush();
			}else{
				$reward->setType($this->container->getParameter('init_one'));
				$reward->setRewards($this->container->getParameter('init'));
				$em->persist($reward);
				$em->flush();
			}
			return true;
		}else{
			return false;
		}
			
	}

	//完善后领取(积分或优惠券）
    private function getReward($componType,$id){
    	$em = $this->getDoctrine()->getManager();
    	if($componType == 'point'){
			$this->getPointReward($componType,$id);
			return $this->container->getParameter('init_one');
		}else{
			$amazonCount = $em->getRepository('JiliApiBundle:AmazonCoupon')->countCoupon();
			if($amazonCount == $this->container->getParameter('init')){
				$isuserAmazon = $em->getRepository('JiliApiBundle:AmazonCoupon')->findByUserid($id);
				if(empty($isuserAmazon)){
					$this->getPointReward($componType,$id);
					return $this->container->getParameter('init_three');
				}else{
					return false;
				}
			}else{
				if($this->getCoupons($id)){
					return $this->container->getParameter('init_two');
				}else{
					return false;
				}
				
			}
			
		}

    }


    /**
	 * @Route("/amazonResult", name="_user_amazonResult")
	 */
	public function amazonResultAction(){
		$em = $this->getDoctrine()->getManager();
		$request = $this->get('request');
		$couponOd = '';
        $couponElec = '';
        $uid = '';
		$uid = $request->getSession()->get('uid');
		if($uid){
			$userCoupon = $em->getRepository('JiliApiBundle:AmazonCoupon')->findByUserid($uid);
			if(!empty($userCoupon)){
				$couponOd = $userCoupon[0]->getCouponOd();
           		$couponElec = $userCoupon[0]->getCouponElec();
			}
		}else{
			return $this->redirect($this->generateUrl('_default_index'));
		}
		return $this->render('JiliApiBundle:User:amazonResult.html.twig',array(
							'couponOd'=>$couponOd,
							'couponElec'=>$couponElec,
							'uid'=>$uid
							)); 
	}

    /**
	 * @Route("/isExistInfo", name="_user_isExistInfo")
	 */
	public function isExistInfoAction(){
		$code = '';
		$request = $this->get('request');
		$id = $request->getSession()->get('uid');
		$em = $this->getDoctrine()->getManager();
		$user = $em->getRepository('JiliApiBundle:User')->find($id);
		if($user->getSex() && $user->getBirthday() && $user->getProvince() && $user->getCity() && $user->getIncome() && $user->getHobby())
			$code = $this->container->getParameter('init');
		else
			$code = $this->container->getParameter('init_one');
		return new Response($code);
	}

	/**
	 * @Route("/activty", name="_user_activty")
	 */
	public function activtyAction(){
		$componType = 'activity';
		return new Response($componType);
	}
	
	/**
	 * @Route("/province", name="_user_province")
	 */
	public function provinceAction(){
		$arr = array();
		$em = $this->getDoctrine()->getManager();
		$province = $em->getRepository('JiliApiBundle:ProvinceList')->findAll();
		foreach ($province as $key => $value) {
			$arr[] = array('id'=>$value->getId(),'provinceName'=>$value->getProvinceName());
		}
		return new Response(json_encode($arr));
	}

	/**
	 * @Route("/hobby", name="_user_hobby")
	 */
	public function hobbyAction(){
		$arr = array();
		$em = $this->getDoctrine()->getManager();
		$hobby = $em->getRepository('JiliApiBundle:HobbyList')->findAll();
		foreach ($hobby as $key => $value) {
			$arr[] = array('id'=>$value->getId(),'hobby'=>$value->getHobbyName());
		}
		return new Response(json_encode($arr));
	}

	/**
	 * @Route("/income", name="_user_income")
	 */
	public function incomeAction(){
		$arr = array();
		$em = $this->getDoctrine()->getManager();
		$income = $em->getRepository('JiliApiBundle:MonthIncome')->findAll();
		foreach ($income as $key => $value) {
			$arr[] = array('id'=>$value->getId(),'income'=>$value->getIncome());
		}
		return new Response(json_encode($arr));
	}
	
			
	/**
	 * @Route("/userInfo", name="_user_userInfo")
	 */
	public function userInfoAction(){
		$arr = array();
		$mobile = '';
		$sex = '';
		$request = $this->get('request');
		$id = $request->getSession()->get('uid');
		$em = $this->getDoctrine()->getManager();
		$user = $em->getRepository('JiliApiBundle:User')->find($id);
		$mobile = $user->getTel();
		$sex = $user->getSex();
		$arr[] = array(
						'id'=>$user->getId(),
						'email'=>$user->getEmail(),
						'nick'=>$user->getNick(),
						'sex'=>$sex,
						'mobile'=>$mobile,
						);
		return new Response(json_encode($arr));
	}

	/**
	 * @Route("/isReward", name="_user_isReward")
	 */
	public function isRewardAction(){
		$componType = 'activity';
		$id = $request->getSession()->get('uid');
		$em = $this->getDoctrine()->getManager();
        if($this->isExistInfo($id)){
        	if($this->isGetReward($id,$componType)){
        		$isgetReward = $this->isGetReward($id,$componType);
        		if($isgetReward ==  $this->container->getParameter('init_two')){
        			$userCoupon = $em->getRepository('JiliApiBundle:AmazonCoupon')->findByUserid($id);
					$getInfoReward = $userCoupon[0]->getCoupon();
        		}
        	}else{
        		$rsReward = $this->getReward($componType,$id);
        		if($rsReward == $this->container->getParameter('init_two')){
        			$userCoupon = $em->getRepository('JiliApiBundle:AmazonCoupon')->findByUserid($id);
					$getInfoReward = $userCoupon[0]->getCoupon();
        		}
        	}
        }else{


        }


	}




	/**
	 * @Route("/registerReward", name="_user_registerReward")
	 */
	public function registerRewardAction(){
		$code = array();
		$codeflag = '';
		$birthday = '';
		$city = '';
		$month_income = '';
		$request = $this->get('request');
		$id = $request->getSession()->get('uid');
		// if(!$id)
		// 	return $this->redirect($this->generateUrl('_default_index'));
		$em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('JiliApiBundle:User')->find($id);
		$sex = $request->request->get('sex');
		$tel = $request->request->get('tel');
		$year = $request->request->get('year');
		$month = $request->request->get('month');
		$provinceId = $request->request->get('province');
		$city = $request->request->get('city');
		$month_income = $request->request->get('income');
		$hobby = $request->request->get('hobby');
		if($sex && $year && $city && $month_income && $hobby){
			if($year){
				$birthday = $year;
				if($month)
					$birthday = $birthday.'-'.$month;
			}	
			if($hobby)
				$hobbys  = substr($hobby,0,strlen($hobby)-1); 
			if($tel){
				if(!preg_match("/^13[0-9]{1}[0-9]{8}$|15[0-9]{1}[0-9]{8}$|18[0-9]{1}[0-9]{8}$/",$tel)){
					$codeflag = $this->container->getParameter('update_wr_mobile');
					$code[] = array("code"=>$this->container->getParameter('init_five'),"msg"=>$codeflag);
				}else{
					$user->setSex($sex);
					$user->setBirthday($birthday);
					$user->setProvince($provinceId);
					$user->setCity($city);
					$user->setTel($tel);
					$user->setIncome($month_income);
					$user->setHobby($hobbys);
					$user->setIsInfoSet($this->container->getParameter('init_one'));
					$em->flush();
					$code[] = array("code"=>$this->container->getParameter('init_one'));  
				}
			}else{
				$user->setSex($sex);
				$user->setBirthday($birthday);
				$user->setProvince($provinceId);
				$user->setCity($city);
				$user->setTel($tel);
				$user->setIncome($month_income);
				$user->setHobby($hobbys);
				$user->setIsInfoSet($this->container->getParameter('init_one'));
				$em->flush();
        		$code[] = array("code"=>$this->container->getParameter('init_one')); 
			}
		}else{
			$codeflag = $this->container->getParameter('reg_mobile');
			$code[] = array("code"=>$this->container->getParameter('init_four'),"msg"=>$codeflag);
		}
	
		return new Response(json_encode($code));
	}


	private function notReadCb(){
		$id = $this->get('request')->getSession()->get('uid');
		$em = $this->getDoctrine()->getManager();
		$user = $em->getRepository('JiliApiBundle:User')->find($id);
		$countCb = $em->getRepository('JiliApiBundle:SendCallboard')->CountAllCallboard($user->getRegisterDate()->format('Y-m-d H:i:s'));
		$countIsCb = $em->getRepository('JiliApiBundle:SendCallboard')->CountIsReadCallboard($id);
		$countUserCb = intval($countCb[0]['num']) - intval($countIsCb[0]['num']);
		return $countUserCb;
	}

	private function notReadMs($id){
		$countUserMs = $this->countSendMs($id);
		return $countUserMs[0]['num'];
	}

	/**
	 * @Route("/isNewMs/{id}", name="_user_isNewMs")
	 */
	public function isNewMsAction($id)
	{
		$countMessage = '';
		if($this->notReadCb() > 0 && $this->notReadMs($id) == 0){
			$countMessage = $this->container->getParameter('init_one');
		}
		return new Response($countMessage);
	}
	


	/**
	 * @Route("/info", name="_user_info",requirements={"_scheme"="https"})
	 */
	public function infoAction()
	{
		// $existUserinfo = '';
		$existUserinfo = $this->container->getParameter('init_one');
		$countMessage = '';
		$code = '';
		$flag = '';
		$codeflag = '';
		$birthday = '';
		$city = '';
		$month_income = '';
		$newYear = $this->container->getParameter('init');
		$newMonth = $this->container->getParameter('init');
		$newHobby = '';
		$disarea = '';
		$usercomes = '';
		$userProHobby = '';
		$daydate =  date("Y-m-d H:i:s", strtotime(' -30 day'));
		$request = $this->get('request');
		$id = $request->getSession()->get('uid');
		$em = $this->getDoctrine()->getManager();
		$user = $em->getRepository('JiliApiBundle:User')->find($id);
		if($user->getHobby()){
			$userProHobby = explode(",",$user->getHobby());
			foreach ($userProHobby as $key => $value) {
				$userHobby = $em->getRepository('JiliApiBundle:HobbyList')->find($value);
				$userHobbyList[] = $userHobby->getHobbyName();
			}
			$newHobby = implode(',',$userHobbyList);
		}

		if($user->getBirthday()){
			if(strlen($user->getBirthday())>5){
				$newBirthday = explode("-",$user->getBirthday());
				$newYear = $newBirthday[0];
				$newMonth = $newBirthday[1];
			}else{
				$newYear = $user->getBirthday();
			}
		}
		$hobbyList = $em->getRepository('JiliApiBundle:HobbyList')->findAll();
		$province = $em->getRepository('JiliApiBundle:ProvinceList')->findAll();
		$income = $em->getRepository('JiliApiBundle:MonthIncome')->findAll();
		$option = array('status' => 0 ,'offset'=>'1','limit'=>'10');
		$option_ex = array('daytype' => 0 ,'offset'=>'1','limit'=>'10');
		$adtaste = $this->selTaskHistory($id,$option);
		foreach ($adtaste as $key => $value) {
			if($value['orderStatus'] == 1 && $value['type'] ==1){
				unset($adtaste[$key]);
			}
		}
		$adtasteNum = count($adtaste);
		$exchange = $em->getRepository('JiliApiBundle:PointsExchange');
		$exchange = $exchange->getUserExchange($id,$option_ex);
		$exFrWen = $em->getRepository('JiliApiBundle:ExchangeFromWenwen')->eFrWenByIdMaxTen($id);
		$sex = $request->request->get('sex');
		$tel = $request->request->get('tel');
		$year = $request->request->get('year');
		$month = $request->request->get('month');
		$provinceId = $request->request->get('province');
		$city = $request->request->get('city');
		$month_income = $request->request->get('income');
		$hobby = $request->request->get('hobby');
		$form  = $this->createForm(new RegType(), $user);
		if ($request->getMethod() == 'POST') {
			if($request->request->get('update')){
				if($sex && $year && $city && $month_income && $hobby){
					if($year){
						$birthday = $year;
						if($month)
							$birthday = $birthday.'-'.$month;
					}	
					if($hobby)
						$hobbys = implode(",",$hobby);
					if($tel){
						if(!preg_match("/^13[0-9]{1}[0-9]{8}$|15[0-9]{1}[0-9]{8}$|18[0-9]{1}[0-9]{8}$/",$tel)){
							$codeflag = $this->container->getParameter('update_wr_mobile');
						}else{
							$user->setSex($sex);
							$user->setBirthday($birthday);
							$user->setProvince($provinceId);
							$user->setCity($city);
							$user->setTel($tel);
							$user->setIncome($month_income);
							$user->setHobby($hobbys);
							$user->setIsInfoSet($this->container->getParameter('init_one'));
							$em->flush();
							return $this->redirect($this->generateUrl('_user_info'));
						}
					}else{
						$user->setSex($sex);
						$user->setBirthday($birthday);
						$user->setProvince($provinceId);
						$user->setCity($city);
						$user->setTel($tel);
						$user->setIncome($month_income);
						$user->setHobby($hobbys);
						$user->setIsInfoSet($this->container->getParameter('init_one'));
						$em->flush();
						return $this->redirect($this->generateUrl('_user_info'));
					}
				}else{
					$codeflag = $codeflag = $this->container->getParameter('reg_mobile');
				}	
			}else{
				if($request->request->get('reset')){
	    			return $this->redirect($this->generateUrl('_user_info'));
    				
	    		}else{
	    			$form->bindRequest($request);
	    			$path =  $this->container->getParameter('upload_tmp_dir');
	    			$code = $user->upload($path);
	    			if($code == $this->container->getParameter('init_one')){
	    				$code =  $this->container->getParameter('upload_img_type');
	    			} 
	    			if($code == $this->container->getParameter('init_two')){
	    				$code =  $this->container->getParameter('upload_img_size');
	    			}
		    		return new Response(json_encode($code));
	    		}
			} 
		}
		//用户地区
		if($user->getProvince()){
			$userProvince = $em->getRepository('JiliApiBundle:ProvinceList')->find($user->getProvince());
			if($user->getCity()){
				$userCity = $em->getRepository('JiliApiBundle:CityList')->find($user->getCity());
				$disarea = $userProvince->getProvinceName()." ".$userCity->getCityName();
			}else{
				$disarea = $userProvince->getProvinceName();
			}
		}
		//月收入
		if($user->getIncome()){
			$userIncome = $em->getRepository('JiliApiBundle:MonthIncome')->find($user->getIncome());
			$usercomes = $userIncome->getIncome();
		}	
		if($this->notReadCb() == 0 && $this->notReadMs($id)>0){
			$countMessage = $this->container->getParameter('init_one');
		}		
		return $this->render('JiliApiBundle:User:info.html.twig',array( 
				'form' => $form->createView(),
				'form_upload' =>$form->createView(),
				'user' => $user,
				'adtaste' => $adtaste,
				'exchange' => $exchange,
				'code' => $code,
				'codeflag' => $codeflag,
				'existUserinfo' => $existUserinfo,
				'adtasteNum' => $adtasteNum,
				'hobbyList' => $hobbyList,
				'province' => $province,
				'income' => $income,
				'newHobby' => $userProHobby,
				'newYear' => $newYear,
				'newMonth' => $newMonth,
				'userHobby' =>	$newHobby,
			 	'disarea' => $disarea,
				'usercomes'=> $usercomes,
				'sex' => $sex,
				'tel' => $tel,
				'month_income' => $month_income,
				'hobby' => $hobby,
				'year' => $year,
				'month' => $month,
				'provinceId' => $provinceId,
				'city' => $city,
				'countMessage'=>$countMessage,
				'exFrWen'=> $exFrWen
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
		//$session = new Session();
        $session = $this->get('session');
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

        //login
        $loginLister = $this->get('login.listener');
        $code = $loginLister->login($request,$email,$pwd);
        if($code == "ok"){
            $current_url = $request->getSession()->get('goToUrl');
            $request->getSession()->remove('goToUrl');
            return $this->redirect($current_url);
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
			if(empty($passCode)){
				$str = 'jiliforgetpassword';
				$code = md5($id.str_shuffle($str));
				$url = $this->generateUrl('_user_forgetPass',array('code'=>$code,'id'=>$id),true);
				if($this->sendMail_reset($url, $email,$nick)){
					$setPasswordCode = new setPasswordCode();
					$setPasswordCode->setUserId($id);
					$setPasswordCode->setCode($code);
					$setPasswordCode->setIsAvailable($this->container->getParameter('init_one'));
					$em->persist($setPasswordCode);
					$em->flush();
					$code = $this->container->getParameter('init_one');
				}
			}else{
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
	        									$user->setRewardMultiple($this->container->getParameter('init_one'));
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
	 * @Route("/exchangeLook", name="_user_exchangeLook")
	 */
	public function exchangeLook(){
		$code = array();
		$request = $this->get('request');
        $exid = $request->query->get('exid');
		$em = $this->getDoctrine()->getManager();
		$ear = $em->getRepository('JiliApiBundle:ExchangeAmazonResult')->findByExchangeId($exid);

		$code[] = array("a"=>$ear[0]->getAmazonCardOne());
		$code[] = array("a"=>$ear[0]->getAmazonCardTwo());
		$code[] = array("a"=>$ear[0]->getAmazonCardThree());
		$code[] = array("a"=>$ear[0]->getAmazonCardFour());
		$code[] = array("a"=>$ear[0]->getAmazonCardFive());
		return new Response(json_encode($code));
	}
	
	/**
	 * @Route("/exchange/{type}/{exchangeType}", name="_user_exchange")
	 */
	public function exchangeAction($type=0,$exchangeType){
		$id = $this->get('request')->getSession()->get('uid');
        if(!$id){
           return $this->redirect($this->generateUrl('_user_login'));
        }
		$em = $this->getDoctrine()->getManager();
		$user = $em->getRepository('JiliApiBundle:User')->find($id);
		$arr['user'] = $user;
		if($exchangeType==1){
			$repository = $em->getRepository('JiliApiBundle:PointsExchange');
			$option = array('daytype' => $type ,'offset'=>'','limit'=>'');
			$exchange = $repository->getUserExchange($id,$option);
			$arr['exchange'] = $exchange;
			$paginator = $this->get('knp_paginator');
			$arr['pagination'] = $paginator
			        ->paginate($exchange,
					$this->get('request')->query->get('page', 1), $this->container->getParameter('page_num'));
			$arr['pagination']->setTemplate('JiliApiBundle::pagination.html.twig');
		}else if($exchangeType==2){
			$exFrWen = $em->getRepository('JiliApiBundle:ExchangeFromWenwen')->eFrWenById($id);
			$arr['exFrWen'] = $exFrWen;
			$paginator = $this->get('knp_paginator');
			$arr['pagination'] = $paginator
			        ->paginate($exFrWen,
					$this->get('request')->query->get('page', 1), $this->container->getParameter('page_num'));
			$arr['pagination']->setTemplate('JiliApiBundle::pagination.html.twig');

		}else{
			return $this->redirect($this->generateUrl('_default_error'));

		}
		$arr['exchangeType'] = $exchangeType;
		$arr['type'] = $type;
		return $this->render('JiliApiBundle:User:exchange.html.twig',$arr);
	}

	
	/**
	 * @Route("/adtaste/{type}", name="_user_adtaste")
	 */
	public function adtasteAction($type){
		$id = $this->get('request')->getSession()->get('uid');
		$em = $this->getDoctrine()->getManager();
		$option = array('status' => $type ,'offset'=>'','limit'=>'');
		$adtaste = $this->selTaskHistory($id,$option);
		foreach ($adtaste as $key => $value) {
			if($value['orderStatus'] == 1 && $value['type'] ==1){
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
	 * @Route("/updateIsRead", name="_user_updateIsRead")
	 */
	public function updateIsReadAction(){
		$content = '';
		$isRead = '';
		$code = array();
		$request = $this->get('request');
		$id = $request->getSession()->get('uid');
		$sendid = $request->query->get('sendid');
		$em = $this->getDoctrine()->getManager();
		$isreadInfo = $em->getRepository('JiliApiBundle:IsReadCallboard')->isreadInfo($sendid,$id);
		if(empty($isreadInfo)){
			$isRead = new IsReadCallboard();
			$isRead->setSendCbId($sendid);
			$isRead->setUserId($id);
			$em->persist($isRead);
	        $em->flush();
	        $isRead = $this->container->getParameter('init_one');
    	}
		$sendCb = $em->getRepository('JiliApiBundle:SendCallboard')->find($sendid);
		$content = $sendCb->getContent();
		$code[] = array('content'=>$content,'isRead'=>$isRead);
		return new Response(json_encode($code));
	}


	/**
	 * @Route("/updateSendMs", name="_user_updateSendMs")
	 */
	public function updateSendMsAction(){
		$code = array();
		$request = $this->get('request');
		$id = $request->getSession()->get('uid');
		$sendid = $request->query->get('sendid');
		$em = $this->getDoctrine()->getManager();
		$showMs = $this->updateSendMs($id,$sendid);
		return new Response(json_encode($showMs));
	}


	/**
	 * @Route("/message/{sid}",requirements={"sid" = "\d+"}, name="_user_message")
	 */
	public function messageAction($sid){
		$id = $this->get('request')->getSession()->get('uid');
		$em = $this->getDoctrine()->getManager();
		$user = $em->getRepository('JiliApiBundle:User')->find($id);
		$arr['user'] = $user;
		if($sid == $this->container->getParameter('init_two')){//公告
			$sendCb = $em->getRepository('JiliApiBundle:SendCallboard')->getSendCb();	
			$userCb = $em->getRepository('JiliApiBundle:IsReadCallboard')->getUserIsRead($id);	
			$userIsRead = array();
			foreach ($userCb as $keyCb => $valueCb) {
				$userIsRead[$valueCb['sendCbId']] = $valueCb['sendCbId'];
			}
			$reg_date = $user->getRegisterDate()->format('Y-m-d H:i:s');
			foreach ($sendCb as $key => $value) {	
				if($value['createtime']->format('Y-m-d H:i:s') > $reg_date){
					if(array_key_exists($value['id'],$userIsRead))
						$sendCb[$key]['isRead'] = $this->container->getParameter('init_one');
					else
						$sendCb[$key]['isRead'] = '';
				}else{
					unset($sendCb[$key]);
				}
				
			}
			$arr['sendCb'] = $sendCb;
			$paginator = $this->get('knp_paginator');
			$arr['pagination'] = $paginator
			->paginate($sendCb,
					$this->get('request')->query->get('page', 1), $this->container->getParameter('page_num'));
			$arr['pagination']->setTemplate('JiliApiBundle::pagination.html.twig');
		}
		if($sid == $this->container->getParameter('init_one')){//消息
			$showMs  = $this->selectSendMs($id);
			$arr['showMs'] = $showMs;
			$paginator = $this->get('knp_paginator');
			$arr['pagination'] = $paginator
			->paginate($showMs,
					$this->get('request')->query->get('page', 1), $this->container->getParameter('page_num'));
			$arr['pagination']->setTemplate('JiliApiBundle::pagination.html.twig');

		}
		$arr['sid'] = $sid;
		return $this->render('JiliApiBundle:User:message.html.twig',$arr);
	}


	/**
	* @Route("/countMs", name="_user_countMs")
	*/
	public function countMsAction(){
		$notRead = $this->container->getParameter('init');
		$id = $this->get('request')->getSession()->get('uid');
		$em = $this->getDoctrine()->getManager();
		$user = $em->getRepository('JiliApiBundle:User')->find($id);
		$countCb = $em->getRepository('JiliApiBundle:SendCallboard')->CountAllCallboard($user->getRegisterDate()->format('Y-m-d H:i:s'));
		$countIsCb = $em->getRepository('JiliApiBundle:SendCallboard')->CountIsReadCallboard($id);
		$countMs = $this->countSendMs($id);
		$notRead = intval($countMs[0]['num']) + intval($countCb[0]['num']) - intval($countIsCb[0]['num']);
 		return new Response($notRead);
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


	private function updateSendMs($userid,$sendid){
		$isRead = '';
		$code = array();
		if(strlen($userid)>1){
			$uid = substr($userid,-1,1);
		}else{
			$uid = $userid;
		}
		$em = $this->getDoctrine()->getManager();
		switch($uid){
		case 0:
		      $sm = $em->getRepository('JiliApiBundle:SendMessage00');
		      break;
		case 1:
		      $sm = $em->getRepository('JiliApiBundle:SendMessage01');
		      break;
		case 2:
		      $sm = $em->getRepository('JiliApiBundle:SendMessage02');
		      break;
		case 3:
		      $sm = $em->getRepository('JiliApiBundle:SendMessage03');
		      break;
		case 4:
		      $sm = $em->getRepository('JiliApiBundle:SendMessage04');
		      break;
		case 5:
		      $sm = $em->getRepository('JiliApiBundle:SendMessage05');
		      break;
		case 6:
		      $sm = $em->getRepository('JiliApiBundle:SendMessage06');
		      break;
		case 7:
		      $sm = $em->getRepository('JiliApiBundle:SendMessage07');
		      break;
		case 8:
		      $sm = $em->getRepository('JiliApiBundle:SendMessage08');
		      break;
		case 9:
		      $sm = $em->getRepository('JiliApiBundle:SendMessage09');
		      break;
		}

		$updateSm = $sm->find($sendid);
		if($updateSm->getReadFlag() == 0){
			$updateSm->setReadFlag($this->container->getParameter('init_one'));
			$em->persist($updateSm);
			$em->flush();
			$isRead = $this->container->getParameter('init_one');
		}
		$code[] = array('content'=>$updateSm->getContent(),'isRead'=>$isRead);
		return $code;
    }


    private function countSendMs($userid){
      if(strlen($userid)>1){
            $uid = substr($userid,-1,1);
      }else{
            $uid = $userid;
      }
      $em = $this->getDoctrine()->getManager();
      switch($uid){
            case 0:
                  $sm = $em->getRepository('JiliApiBundle:SendMessage00');
                  break;
            case 1:
                  $sm = $em->getRepository('JiliApiBundle:SendMessage01');
                  break;
            case 2:
                  $sm = $em->getRepository('JiliApiBundle:SendMessage02');
                  break;
            case 3:
                  $sm = $em->getRepository('JiliApiBundle:SendMessage03');
                  break;
            case 4:
                  $sm = $em->getRepository('JiliApiBundle:SendMessage04');
                  break;
            case 5:
                  $sm = $em->getRepository('JiliApiBundle:SendMessage05');
                  break;
            case 6:
                  $sm = $em->getRepository('JiliApiBundle:SendMessage06');
                  break;
            case 7:
                  $sm = $em->getRepository('JiliApiBundle:SendMessage07');
                  break;
            case 8:
                  $sm = $em->getRepository('JiliApiBundle:SendMessage08');
                  break;
            case 9:
                  $sm = $em->getRepository('JiliApiBundle:SendMessage09');
                  break;
      }
 	  $countMs = $sm->CountSendMs($userid);
 	  return $countMs; 
    }


	private function selectSendMs($userid){
      if(strlen($userid)>1){
            $uid = substr($userid,-1,1);
      }else{
            $uid = $userid;
      }
      $em = $this->getDoctrine()->getManager();
      switch($uid){
            case 0:
                  $sm = $em->getRepository('JiliApiBundle:SendMessage00');
                  break;
            case 1:
                  $sm = $em->getRepository('JiliApiBundle:SendMessage01');
                  break;
            case 2:
                  $sm = $em->getRepository('JiliApiBundle:SendMessage02');
                  break;
            case 3:
                  $sm = $em->getRepository('JiliApiBundle:SendMessage03');
                  break;
            case 4:
                  $sm = $em->getRepository('JiliApiBundle:SendMessage04');
                  break;
            case 5:
                  $sm = $em->getRepository('JiliApiBundle:SendMessage05');
                  break;
            case 6:
                  $sm = $em->getRepository('JiliApiBundle:SendMessage06');
                  break;
            case 7:
                  $sm = $em->getRepository('JiliApiBundle:SendMessage07');
                  break;
            case 8:
                  $sm = $em->getRepository('JiliApiBundle:SendMessage08');
                  break;
            case 9:
                  $sm = $em->getRepository('JiliApiBundle:SendMessage09');
                  break;
      }
 	  $showMs = $sm->getSendMsById($userid);
 	  return $showMs;
     
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
			if($value['type']==1 ) {

				$adUrl = $task->getUserAdwId($value['orderId']);

				$po[$key]['adid'] = $adUrl[0]['adid'];

			}else{
				$po[$key]['adid'] = '';
			}
		}
		return $po;

    }
	
}
