<?php
namespace Jili\ApiBundle\Controller;
use Jili\ApiBundle\Entity\User;
use Jili\ApiBundle\Entity\PointsExchange;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Cookie;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Jili\ApiBundle\Mailer;
use Jili\ApiBundle\Form\RegType;
use Jili\ApiBundle\Entity\LoginLog;
use Jili\ApiBundle\Entity\WenwenUser;
use Jili\ApiBundle\Entity\CallBoard;
use Jili\ApiBundle\Entity\UserGameVisit;
use Jili\ApiBundle\Entity\RegisterReward;
use Jili\ApiBundle\Entity\UserInfoVisit;
use Jili\ApiBundle\Entity\CheckinAdverList;
use Jili\ApiBundle\Entity\CheckinUserList;
use Jili\ApiBundle\Entity\CheckinClickList;
use Jili\ApiBundle\Entity\CheckinPointTimes;
use Jili\ApiBundle\Entity\UserWenwenVisit;

class DefaultController extends Controller {
	// 是否完善资料
	private function isExistInfo($userid) {
		$em = $this->getDoctrine()->getManager();
		$user = $em->getRepository('JiliApiBundle:User')->find($userid);
		if ($user->getSex() && $user->getBirthday() && $user->getProvince() && $user->getCity() && $user->getIncome() && $user->getHobby())
			return true;
		else
			return false;
	}

	//是否给过奖励
	private function isGetReward($userid) {
		$em = $this->getDoctrine()->getManager();
		$user = $em->getRepository('JiliApiBundle:RegisterReward')->findByUserid($userid);
		if ($user) {
			if ($user[0]->getType() == $this->container->getParameter('init_one')) {
				$isuserAmazon = $em->getRepository('JiliApiBundle:AmazonCoupon')->findByUserid($userid);
				if (empty ($isuserAmazon)) {
					return $this->container->getParameter('init_three'); //获得米粒的
				} else {
					return $this->container->getParameter('init_two'); //获得优惠券的
				}
			} else {
				return $this->container->getParameter('init_four'); //参加其他活动领取的
			}
		} else {
			return false;
		}

	}

	//给米粒奖励
	private function getPointReward($componType, $userid) {
		$em = $this->getDoctrine()->getManager();
		$isuserPoint = $em->getRepository('JiliApiBundle:RegisterReward')->findByUserid($userid);
		if (empty ($isuserPoint)) {
			$reward = new RegisterReward();
			$reward->setUserId($userid);
			if ($componType == 'point') {
				$reward->setType($this->container->getParameter('init_two'));
				$reward->setRewards($this->container->getParameter('init_fivty'));
				$em->persist($reward);
				$em->flush();
				$this->getPointHistory($userid, $this->container->getParameter('init_fivty'));
				$user = $em->getRepository('JiliApiBundle:User')->find($userid);
				$user->setPoints(intval($user->getPoints() + $this->container->getParameter('init_fivty')));
				$em->persist($user);
				$em->flush();
			} else {
				$reward->setType($this->container->getParameter('init_one'));
				$reward->setRewards($this->container->getParameter('init'));
				$em->persist($reward);
				$em->flush();
			}
			return true;
		} else {
			return false;
		}

	}

	//完善后领取(积分或优惠券）
	private function getReward($componType, $id) {
		$em = $this->getDoctrine()->getManager();
		if ($componType == 'point') {
			$this->getPointReward($componType, $id);
			return $this->container->getParameter('init_one');
		} else {
			$amazonCount = $em->getRepository('JiliApiBundle:AmazonCoupon')->countCoupon();
			if ($amazonCount == $this->container->getParameter('init')) {
				$isuserAmazon = $em->getRepository('JiliApiBundle:AmazonCoupon')->findByUserid($id);
				if (empty ($isuserAmazon)) {
					$this->getPointReward($componType, $id);
					return $this->container->getParameter('init_three');
				} else {
					return false;
				}
			} else {
				if ($this->getCoupons($id)) {
					return $this->container->getParameter('init_two');
				} else {
					return false;
				}

			}

		}

	}
	//领取亚马逊优惠券
	private function getCoupons($id) {
		$em = $this->getDoctrine()->getManager();
		$isuserAmazon = $em->getRepository('JiliApiBundle:AmazonCoupon')->findByUserid($id);
		if (empty ($isuserAmazon)) {
			$amazon = $em->getRepository('JiliApiBundle:AmazonCoupon')->getAmcoupon();
			// $getCoupon = $amazon['0']['coupon'];
			$amazonCoupon = $em->getRepository('JiliApiBundle:AmazonCoupon')->find($amazon['0']['id']);
			$amazonCoupon->setUserId($id);
			$em->flush();
			$reward = new RegisterReward();
			$reward->setUserId($id);
			$reward->setType($this->container->getParameter('init_one'));
			$em->persist($reward);
			$em->flush();
			return true;
		} else {
			return false;
		}

	}

	//签到列表
	public function checkinList(){
		$arrList = array();
		$date = date('Y-m-d H:i:s');
		$cal_count = "";
		$campaign_multiple = $this->container->getParameter('campaign_multiple');
		$request = $this->get('request');
		$uid = $request->getSession()->get('uid');
		$em = $this->getDoctrine()->getManager();	
		$user = $em->getRepository('JiliApiBundle:User')->find($uid);
        $reward_multiple = $user->getRewardMultiple();
		$cal = $em->getRepository('JiliApiBundle:CheckinAdverList')->showCheckinList($uid);
        if (count($cal) > 6) {
            $cal_count = 6;
            $calNow = array_rand($cal, 6); //随机取数组中6个键值
        } else {
            $cal_count = count($cal);
            for ($i = 0; $i < count($cal); $i++) {
                $calNow[$i] = $i;
            }
        }
        for ($i = 0; $i < $cal_count; $i++) {
            $cps_rate = $reward_multiple > $campaign_multiple ? $reward_multiple : $campaign_multiple;
            $cal[$calNow[$i]]['reward_rate'] = $cal[$calNow[$i]]['incentive_rate'] * $cal[$calNow[$i]]['reward_rate'] * $cps_rate;
            $cal[$calNow[$i]]['reward_rate'] = round($cal[$calNow[$i]]['reward_rate'] / 10000, 2);
            $arrList[] = $cal[$calNow[$i]];
        }
		return $arrList;

	}


	public function clickDayCount(){
		$culTimes = $this->container->getParameter('init');
		$date = date('Y-m-d');
		$request = $this->get('request');
		$uid = $request->getSession()->get('uid');
		$em = $this->getDoctrine()->getManager();	
		$culTimes = $em->getRepository('JiliApiBundle:CheckinUserList')->countUserList($uid,$date);
		return $culTimes;

	}


	public function conUnion($str) {
		$pattern = '/[^\x00-\x80]/';
		if (preg_match($pattern, $str)) {
			return true; // "含有中文";
		} else {
			return false;
		}
	}

	public function isUnion($str) {
		if (!eregi("[^\x80-\xff]", "$str")) {
			return true; //全是中文
		} else {
			return false;
		}
	}

	public function my_substr($str, $start, $len) {
		$tmpstr = "";
		$strlen = $start + $len;
		for ($i = 0; $i < $strlen; $i++) {
			if (ord(substr($str, $i, 1)) > 0xa0) {
				$tmpstr .= substr($str, $i, 3);
				$i += 2;
			} else
				$tmpstr .= substr($str, $i, 1);
		}
		return $tmpstr;
	}

	public function countStrs($str) {
		$len = strlen($str);
		$i = 0;
		while ($i < $len) {
			if (preg_match("/^[" . chr(0xa1) . "-" . chr(0xff) . "]+$/", $str[$i])) {
				$i += 2;
			} else {
				$i += 1;
			}
		}
		return $i;
	}

	public function getToken($email) {
		$seed = "ADF93768CF";
		$hash = sha1($email . $seed);
		for ($i = 0; $i < 5; $i++) {
			$hash = sha1($hash);
		}
		return $hash;
	}

	/**
	* @Route("/landing", name="_default_landing",requirements={"_scheme"="https"})
	*/
	public function landingAction() {
		if ($this->get('request')->getSession()->get('uid')) {
			return $this->redirect($this->generateUrl('_homepage'));
		}
        $email = '';
		$is_user = '';
		$code = '';
        $err_msg = '';
        $signature = '';
        $uniqkey = '';
		$request = $this->get('request');
		$token = $request->query->get('secret_token');
		$nick = $request->request->get('nick');
		$pwd = $request->request->get('pwd');
		$newPwd = $request->request->get('newPwd');
		if ($token) {
			$request->getSession()->remove('token');
			$request->getSession()->set('token', $token);
		}
		$u_token = $request->getSession()->get('token');
		if (!$u_token) {
			return $this->redirect($this->generateUrl('_user_reg'));
		}
		$em = $this->getDoctrine()->getManager();
		$wenuser = $em->getRepository('JiliApiBundle:WenwenUser')->findByToken($u_token);
		if (!$wenuser) {
			$params = json_decode(base64_decode(strtr($u_token, '-_', '+/')));
			if ($params) {
				$email = $params->email;
				$signature = $params->signature;
				if (isset ($params->uniqkey))
					$uniqkey = $params->uniqkey;
			}
			if ($this->getToken($email) != $signature) {
                return $this->redirect($this->generateUrl('_user_reg'));
            }
		} else {
			$email = $wenuser[0]->getEmail();
		}

        $is_email = $em->getRepository('JiliApiBundle:User')->getWenwenUser($email);
        if ($is_email) {
            $is_user = $this->container->getParameter('init_one');
        } else {
            if ($request->getMethod() == 'POST') {
                $err_msg = $this->checkLanding($email, $nick, $pwd, $newPwd);
                if(!$err_msg){
                    $isset_email = $em->getRepository('JiliApiBundle:User')->findByEmail($email);
                    if ($isset_email) {
                        $isset_email[0]->setNick($nick);
                        $isset_email[0]->setPwd($pwd);
                        $isset_email[0]->setIsFromWenwen($this->container->getParameter('init_one'));
                        $isset_email[0]->setRewardMultiple($this->container->getParameter('init_one'));
                        if($uniqkey){
                            $isset_email[0]->setUniqkey($uniqkey);
                        }
                        $em->persist($isset_email[0]);
                        $em->flush();
                        $id = $isset_email[0]->getId();
                        $user = $isset_email[0];
                    } else {
                        $user = new User();
                        $user->setNick($nick);
                        $user->setPwd($pwd);
                        $user->setEmail($email);
                        $user->setIsFromWenwen($this->container->getParameter('init_one'));
                        $user->setPoints($this->container->getParameter('init'));
                        $user->setRewardMultiple($this->container->getParameter('init_one'));
                        $user->setIsInfoSet($this->container->getParameter('init'));

                        if($uniqkey){
                            $user->setUniqkey($uniqkey);
                        }
                        $em->persist($user);
                        $em->flush();
                        $id = $user->getId();
                    }

                    //设置密码之后，注册成功，发邮件2014-01-10
                    $soapMailLister = $this->get('soap.mail.listener');
                    $soapMailLister->setCampaignId($this->container->getParameter('register_success_campaign_id')); //活动id
                    $soapMailLister->setMailingId($this->container->getParameter('register_success_mailing_id')); //邮件id
                    $soapMailLister->setGroup(array ('name' => '积粒网','is_test' => 'false')); //group
                    $recipient_arr = array (
                        array (
                            'name' => 'email',
                            'value' => $email
                        )
                    );
                    $soapMailLister->sendSingleMailing($recipient_arr);
                    $session = $request->getSession();
                    $session->remove('token');
#                    $session->set('uid', $id);
#                    $session->set('nick', $nick);
                    $this->get('login.listener')->initSession( $user );
                    $this->get('login.listener')->checkNewbie( $user );

                    $this->get('login.listener')->log( $user );
                    return $this->redirect($this->generateUrl('_homepage'));
                }
            }
        }

        //最新动态
        $filename = $this->container->getParameter('file_path_recent_point');
        $recentPoint = $this->readFileContent($filename);
        $recent = array();
        if( is_array($recentPoint)) {
            foreach ($recentPoint as $key => $item){
                if($key > 9){
                    break;
                }
                if($item[2] > 0) {
                    $recent[]['title'] = $item[0]."通过".$item[3]."获得".$item[2]."积分";
                }else{
                    $recent[]['title'] = $item[0]."将".(-$item[2])."积分兑换成亚马逊礼品卡";
                }
            }
        }

		return $this->render('JiliApiBundle:Default:landing.html.twig', array (
			'code' => $code,
			'is_user' => $is_user,
			'nick' => $nick,
			'email' => $email,
            'err_msg'=> $err_msg,
            'recent'=>  $recent,
		));

	}

    private function checkLanding($email, $nick, $pwd, $newPwd){
        $err_msg = '';
        $em = $this->getDoctrine()->getManager();
        if(!$nick || $nick == "输入昵称"){
            $err_msg = "请输入昵称";
            return $err_msg;
        }
        if(!$pwd || $pwd == "输入密码"){
            $err_msg = "请输入密码和确认密码";
            return $err_msg;
        }
        if(!$newPwd || $newPwd == "确认密码"){
            $err_msg = "请输入密码和确认密码";
            return $err_msg;
        }
//        if(!$nick || !$pwd || !$newPwd){
//            //$code = $this->container->getParameter('init_five');
//            $err_msg = "都为必填项";
//            return $err_msg;
//        }
        if (!preg_match("/^[\x{4e00}-\x{9fa5}a-zA-Z0-9_]{2,20}$/u", $nick)) {
            //$code = $this->container->getParameter('init_one');
            $err_msg = "昵称为2-20个字符";
            return $err_msg;
        }
        $user_nick = $em->getRepository('JiliApiBundle:User')->findNick($email, $nick);
        if ($user_nick){
            //$code = $this->container->getParameter('init_two');
            $err_msg = "昵称已经注册";
            return $err_msg;
        }
        if (!preg_match("/^[0-9A-Za-z_]{6,20}$/", $pwd)) {
            //$code = $this->container->getParameter('init_three');
            $err_msg = "密码为6-20个字符，不能含特殊符号";
            return $err_msg;
        }
        if ($pwd != $newPwd) {
            //$code = $this->container->getParameter('init_four');
            $err_msg = "2次密码不相同";
            return $err_msg;
        }
        return $err_msg;
    }

	/**
	 * @Route("/isExistVist", name="_default_isExistVist")
     * @Method("POST");
	 */
	public function isExistVistAction() {
		$day = date('Ymd');
		$request = $this->get('request');
		$em = $this->getDoctrine()->getManager();
		$id = $request->getSession()->get('uid');
		if ($id) {
			$visit = $em->getRepository('JiliApiBundle:UserGameVisit')->getGameVisit($id, $day);
			if (empty ($visit)) {
				$code = $this->container->getParameter('init_one');
			} else {
				$code = $this->container->getParameter('init');
			}
		} else {
			$code = $this->container->getParameter('init');
		}
		return new Response($code);

	}

	/**
	 * @Route("/infoVisit", name="_default_infoVisit")
     * @Method("POST")
	 */
	public function infoVisitAction() {
		$day = date('Ymd');
        $logger = $this->get('logger');

		$request = $this->get('request');
		$em = $this->getDoctrine()->getManager();
		$id = $request->getSession()->get('uid');
		if ($id) {

			$visit = $em->getRepository('JiliApiBundle:UserInfoVisit')->getInfoVisit($id, $day);
			if (empty ($visit)) {
				$infoVisit = new UserInfoVisit();
				$infoVisit->setUserId($id);
				$infoVisit->setVisitDate($day);
				$em->persist($infoVisit);
				$em->flush();
				$code = $this->container->getParameter('init_one');
            } else {
#                $logger->debug('{jarod}'.implode(':', array(__FILE__,__LINE__,'visit', '' )). var_export($visit, true));
                $code = $this->container->getParameter('init');
            }
		} else {
#        $logger->debug('{jarod}'.implode(':', array(__FILE__,__LINE__,'id', '' )). var_export($id, true));
			$code = $this->container->getParameter('init');
		}
		return new Response($code);

	}

	/**
	 * @Route("/gameVisit", name="_default_gameVisit")
	 */
	public function gameVisitAction() {
		$request = $this->get('request');
		$em = $this->getDoctrine()->getManager();
		$id = $request->getSession()->get('uid');
		if ($id) {
            $day = date('Ymd');

            // TODO: use the session value instead of the db query.
			$visit = $em->getRepository('JiliApiBundle:UserGameVisit')->getGameVisit($id, $day);
			if ( empty ($visit) ) {
				$gameVisit = new UserGameVisit();
				$gameVisit->setUserId($id);
				$gameVisit->setVisitDate($day);
				$em->persist($gameVisit);
				$em->flush();

                // remove from session cache.
                $taskList = $this->get('session.task_list');
                $taskList->remove(array( 'game_visit'));
			}
			$code = $this->container->getParameter('init_one');
		} else {
			$code = $this->container->getParameter('init');
		}
		return new Response($code);

	}

	/**
	 * @Route("/about", name="_default_about", requirements={"_scheme"="http"})
	 */
	public function aboutAction() {
		return $this->render('JiliApiBundle:Default:about.html.twig');
	}

	/**
	 * @Route("/error", name="_default_error", requirements={"_scheme"="http"})
	 */
	public function errorAction() {
		return $this->render('JiliApiBundle::error.html.twig');
	}

	/**
	 * @Route("/services", name="_default_services", requirements={"_scheme"="http"})
	 */
	public function servicesAction() {
		return $this->render('JiliApiBundle::onservice.html.twig');
	}

	/**
	 * @Route("/support", name="_default_support", requirements={"_scheme"="http"})
	 */
	public function supportAction() {
		return $this->render('JiliApiBundle:Default:help.html.twig');
	}

	/**
	 * @Route("/service", name="_default_service", requirements={"_scheme"="http"})
	 */
	public function serviceAction() {
		return $this->render('JiliApiBundle:Default:service.html.twig');
	}

	/**
	 * @Route("/contact", name="_default_contact")
	 */
	public function contactAction() {
		$request = $this->get('request');
		$content = $request->query->get('content');
		$email = $request->query->get('email');
		$code = $this->checkContact($content, $email);
		return new Response($code);
	}

	public function checkContact($content, $email) {
		$code = 0;
		//check content null
		if (!$content) {
			$code = 1;
			return $code;
		}
		//check email null
		if (!$email) {
			$code = 2;
			return $code;
		}
		//check email format
		if (!preg_match("/^[A-Za-z0-9-_.+%]+@[A-Za-z0-9-.]+\.[A-Za-z]{2,4}$/", $email)) {
			$code = 3;
			return $code;
		}

		//get user info
		$session = $this->getRequest()->getSession();
		$nick = $session->get('nick');

		//send email
		$subject = "来自非91jili会员的咨询";
		if ($nick) {
			$id = $session->get('uid');
			$em = $this->getDoctrine()->getManager();
			$user = $em->getRepository('JiliApiBundle:User')->find($id);
			$subject = "来自" . $nick . " [" . $user->getEmail() . "] 的咨询";
		}

		$transport = \Swift_SmtpTransport :: newInstance('smtp.exmail.qq.com', 25)->setUsername('contact@91jili.com')->setPassword('91jili');
		$mailer = \Swift_Mailer :: newInstance($transport);
		$message = \Swift_Message :: newInstance()->setSubject($subject)->setFrom(array (
			'contact@91jili.com' => '积粒网'
		))->setTo('cs@91jili.com')->setBody('<html>' .
		'<head></head>' .
		'<body>' .
		'咨询内容<br/>' .
		$content . '<br/><br/>' .
		'联系方式<br/>' .
		$email . '<br/><br/>' .
		'浏览器<br/>'.$_SERVER['HTTP_USER_AGENT'] . '<br/>' .
		'</body>' .
		'</html>', 'text/html');
		$flag = $mailer->send($message);
		if (!$flag) {
			$code = 4;
		}
		return $code;
	}

	public function readFileContent($filename) {

		$contents = null;
		if (!file_exists($filename)) {
			//die("指定文件不存在，操作中断!");
			return $contents;
		}

		//读文件内容
		$file_handle = fopen($filename, "r");
		if (!$file_handle) {
			//die("指定文件不能打开，操作中断!");
			return $contents;
		}

		while (!feof($file_handle)) {
			$line = fgets($file_handle);
			if ($line) {
				$item = explode(",", trim($line));
				$contents[] = $item;
			}
		}

		fclose($file_handle);

		return $contents;
	}

	/**
	* @Route("/wenwenVisit", name="_default_wenwenVisit")
	*/
	public function wenwenVisitAction() {
		$day = date('Ymd');
		$request = $this->get('request');
		$em = $this->getDoctrine()->getManager();
		$id = $request->getSession()->get('uid');
		if ($id) {
			$visit = $em->getRepository('JiliApiBundle:UserWenwenVisit')->getWenwenVisit($id, $day);
			if (empty ($visit)) {
				$wenVisit = new UserWenwenVisit();
				$wenVisit->setUserId($id);
				$wenVisit->setVisitDate($day);
				$em->persist($wenVisit);
				$em->flush();

                $taskList = $this->get('session.task_list');
                // remove from session cache.
                $taskList->remove(array( '91ww_visit'));
			}
			$code = $this->container->getParameter('init_one');
		} else {
			$code = $this->container->getParameter('init');
		}
		return new Response($code);

	}

	/**
	* @Route("/adLogin", name="_default_ad_login")
	*/
	public function adLoginAction() {
        $request = $this->get('request');
#        $email = $request->query->get('email');
#        $pwd = $request->query->get('pwd');
#        $this->get('logger')->debug('{jarod}'.implode(':', array(__FILE__,__LINE__,'email', '' )). var_export($email, true));
#        $this->get('logger')->debug('{jarod}'.implode(':', array(__FILE__,__LINE__,'pwd', '' )). var_export($pwd, true));
#        $loginLister = $this->get('login.listener');
        $code =$this->get('login.listener')->login($this->get('request'));
		return new Response($code);
	}
}
#	/**
#	 * @Route("/index", name="_default_index",requirements={"_scheme"="https"})
#	 */
#	public function indexAction() {
#		if ($_SERVER['HTTP_HOST'] == '91jili.com')
#			return $this->redirect('http://www.91jili.com');
#		$request = $this->get('request');
#		$cookies = $request->cookies;
#		if ($cookies->has('jili_uid') && $cookies->has('jili_nick')) {
#			$this->get('request')->getSession()->set('uid', $cookies->get('jili_uid'));
#			$this->get('request')->getSession()->set('nick', $cookies->get('jili_nick'));
#		}
#		$arr['user'] = array();
#		$arr['arrList'] = array();
#		$arr['checkinPoint'] = '';
#		$arr['limitNick'] = '';
#		$em = $this->getDoctrine()->getManager();
#		$id = $request->getSession()->get('uid');
#		$reward_multiple = '';
#		//截取nick
#		$limitNick = '';
#        $glideAd = false;//是否显示签到活动广告
#        $signRemind = false;//是否显示签到活动广告
#        $signRemind_reg = false;//是否为当天注册
#		if ($id) {
#			$user = $em->getRepository('JiliApiBundle:User')->find($id);
#            $reg_date = $user->getRegisterDate()->format('Y-m-d');
#            //判断是否为当天注册
#            if(date('Y-m-d') == $reg_date){
#            	$signRemind_reg = true;
#            }
#
#			if ($this->countStrs($user->getNick()) > 15) {
#				if ($this->isUnion($user->getNick())){
#					$limitNick = $this->my_substr($user->getNick(), 0, 18) . '...';
#				} else {
#					if ($this->conUnion($user->getNick()))
#						$limitNick = $this->my_substr($user->getNick(), 0, 15) . '...';
#					else
#						$limitNick = $this->my_substr($user->getNick(), 0, 12) . '...';
#				}
#				
#			} 
#			$reward_multiple = $user->getRewardMultiple();
#			$arr['user'] = $user;
#			$arr['limitNick'] = $limitNick;
#			$arr['arrList'] = $this->checkinList();
#            //获取签到积分
#            $checkInLister = $this->get('check_in.listener');
#			$arr['checkinPoint'] = $checkInLister->getCheckinPoint($this->get('request'));;
#			$arr['clickDayCount'] = $this->clickDayCount();
#		}else{
#			$signRemind = false;//未登录，右侧签到活动广告不显示
#            $glideAd = true;//未登录，header签到活动广告显示
#		}
#		$info = '';
#		$couponOd = '';
#		$couponElec = '';
#		$getRewards = '';
#		$banner = '';
#		//补全信息
#		if ($request->query->get('banner') == 'info') {
#			$banner = $this->container->getParameter('init_one');
#			if ($id) {
#				$visit = $em->getRepository('JiliApiBundle:UserInfoVisit')->findByUserid($id);
#				if (!empty ($visit[0])) {
#					$em->remove($visit[0]);
#					$em->flush();
#				}
#				if ($this->isExistInfo($id)) {
#					$info = $this->isGetReward($id);
#					if ($info) {
#						if ($info == $this->container->getParameter('init_two')) {
#							$userCoupon = $em->getRepository('JiliApiBundle:AmazonCoupon')->findByUserid($id);
#							$couponOd = $userCoupon[0]->getCouponOd();
#							$couponElec = $userCoupon[0]->getCouponElec();
#						}
#					} else {
#						$getRewards = $this->getReward('activity', $id);
#						if ($getRewards == $this->container->getParameter('init_two')) {
#							$userCoupon = $em->getRepository('JiliApiBundle:AmazonCoupon')->findByUserid($id);
#							$couponOd = $userCoupon[0]->getCouponOd();
#							$couponElec = $userCoupon[0]->getCouponElec();
#						}
#					}
#
#				} else {
#					$info = $this->container->getParameter('init_one');
#				}
#			}
#		}
#		$arr['info'] = $info;
#		$arr['couponOd'] = $couponOd;
#		$arr['couponElec'] = $couponElec;
#		$arr['getRewards'] = $getRewards;
#		$arr['banner'] = $banner;
#		$code = '';
#//		$arr['userInfo'] = array ();
#		$email = $request->get('email');
#		$pwd = $request->get('pwd');
#		$arr['email'] = $email;
#
#        //首页登录
#        $loginLister = $this->get('login.listener');
#        $code = $loginLister->login($this->get('request'),$email,$pwd);
#        if($code == "ok"){
#        	return $this->redirect($this->generateUrl('_homepage'));
#        }
#
#		$arr['code'] = $code;
#
#		//最新公告，取6条，右三
#		$callboard = $em->getRepository('JiliApiBundle:CallBoard')->getCallboardLimit(6);
#
#		//商家活动:取8条，左二
#		$market = $em->getRepository('JiliApiBundle:MarketActivity')->getActivityList($this->container->getParameter('init_eight'));
#
#		//最新动态 :从文件中读取，左三
#		$filename = $this->container->getParameter('file_path_recent_point');
#		$recentPoint = $this->readFileContent($filename);
#
#		//排行榜 :从文件中读取，右下
#		$filename = $this->container->getParameter('file_path_ranking_month');
#		$rankingMonth = $this->readFileContent($filename);
#		$filename = $this->container->getParameter('file_path_ranking_year');
#		$rankingYear = $this->readFileContent($filename);
#
#		//热门商家:取10条，左下
#		$adverRecommand = $em->getRepository('JiliApiBundle:Advertiserment')->getAdvertiserAreaList($this->container->getParameter('init_four'), 10);
#		foreach ($adverRecommand as $key => $value) {
#			$campaign_multiple = $this->container->getParameter('campaign_multiple');
#			if ($reward_multiple) {
#				if ($value['incentiveType'] == 2) {
#					$cps_rate = $reward_multiple > $campaign_multiple ? $reward_multiple : $campaign_multiple;
#					$adverRecommand[$key]['reward_rate'] = $value['incentiveRate'] * $value['rewardRate'] * $cps_rate;
#					$adverRecommand[$key]['reward_rate'] = round($adverRecommand[$key]['reward_rate'] / 10000, 2);
#				}
#			} else {
#				if ($value['incentiveType'] == 2) {
#					$adverRecommand[$key]['reward_rate'] = $value['incentiveRate'] * $value['rewardRate'] * $campaign_multiple;
#					$adverRecommand[$key]['reward_rate'] = round($adverRecommand[$key]['reward_rate'] / 10000, 2);
#
#				}
#			}
#		}
#
#		//banner,右一
#		$advertiseBanner = $em->getRepository('JiliApiBundle:AdBanner')->getInfoBanner();
#
#		//可以做的任务，签到+游戏+91问问+购物+cpa,右二
#		$day = date('Ymd');
#		$request = $this->get('request');
#		$em = $this->getDoctrine()->getManager();
#		if ($id) {
#			//游戏
#			$visit = $em->getRepository('JiliApiBundle:UserGameVisit')->getGameVisit($id, $day);
#			if (empty ($visit)) {
#				$arr['task']['game'] = $this->container->getParameter('init_one');
#			} else {
#				$arr['task']['game'] = $this->container->getParameter('init');
#			}
#
#			//广告任务墙
#			$visit = $em->getRepository('JiliApiBundle:UserAdvertisermentVisit')->getAdvertisermentVisit($id, $day);
#			if (empty ($visit)) {
#				$arr['task']['ad'] = $this->container->getParameter('init_one');
#			} else {
#				$arr['task']['ad'] = $this->container->getParameter('init');
#			}
#
#			//91wenwen
#			$visit = $em->getRepository('JiliApiBundle:UserWenwenVisit')->getWenwenVisit($id, $day);
#			if (empty ($visit)) {
#				$arr['task']['wen'] = $this->container->getParameter('init_one');
#			} else {
#				$arr['task']['wen'] = $this->container->getParameter('init');
#			}
#
#			//签到
#			$date = date('Y-m-d');
#			$checkin = $em->getRepository('JiliApiBundle:CheckinClickList')->checkStatus($id, $date);
#			if (!empty ($checkin)) {
#				$arr['task']['checkin'] = $this->container->getParameter('init');
#			} else {
#				//获取签到积分
#                $checkInLister = $this->get('check_in.listener');
#                $arr['task']['checkinPoint'] = $checkInLister->getCheckinPoint($this->get('request'));;
#				$arr['task']['checkin'] = $this->container->getParameter('init_one');
#                if($signRemind_reg){
#                	$signRemind = true;//显示签到活动广告
#                }
#			}
#
#			//cpa
#			$repository = $em->getRepository('JiliApiBundle:Advertiserment');
#			$advertise = $repository->getAdvertiserListCPA($id);
#			$arr['advertise'] = $advertise;
#			$arr['task']['cpa'] = $advertise;
#		}
#
#        //advertiserment check
#        $filename = $this->container->getParameter('file_path_advertiserment_check');
#        $arr['adCheck'] = "";
#        if (file_exists($filename)) {
#            $file_handle = fopen($filename, "r");
#            if ($file_handle) {
#               if(filesize ($filename)){
#                    $arr['adCheck'] = fread($file_handle, filesize ($filename));
#               }
#            }
#            fclose($file_handle);
#        }
#
#        //EmergencyAnnouncement
#        $filename = $this->container->getParameter('file_path_emergency_announcement');
#        $arr['emergency_announcement'] = "";
#        if (file_exists($filename)) {
#            $file_handle = fopen($filename, "r");
#            if ($file_handle) {
#               if(filesize ($filename)){
#                    $arr['emergency_announcement'] = fread($file_handle, filesize ($filename));
#               }
#            }
#            fclose($file_handle);
#        }
#
#		$arr['callboard'] = $callboard;
#		$arr['banner_count'] = count($advertiseBanner);
#		$arr['advertise_banner'] = $advertiseBanner;
#
#		$arr['market'] = $market;
#		$arr['recentPoint'] = $recentPoint;
#		$arr['adverRecommand'] = $adverRecommand;
#		$arr['rankingMonth'] = $rankingMonth;
#		$arr['rankingYear'] = $rankingYear;
#		$arr['signRemind'] = $signRemind;
#		$arr['glideAd'] = $glideAd;
#		return $this->render('JiliApiBundle:Default:index.html.twig', $arr);
#	}
