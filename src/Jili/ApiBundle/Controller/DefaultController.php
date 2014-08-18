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
use Symfony\Component\HttpFoundation\RedirectResponse;
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
use Jili\ApiBundle\Entity\UserWenwenVisit;
use Jili\ApiBundle\Utility\FileUtil;

class DefaultController extends Controller
{
    // 是否完善资料
    private function isExistInfo($userid)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('JiliApiBundle:User')->find($userid);
        if ($user->getSex() && $user->getBirthday() && $user->getProvince() && $user->getCity() && $user->getIncome() && $user->getHobby())
            return true;
        else
            return false;
    }

    //是否给过奖励
    private function isGetReward($userid)
    {
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
    private function getPointReward($componType, $userid)
    {
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
    private function getReward($componType, $id)
    {
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
    private function getCoupons($id)
    {
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
    public function checkinList()
    {
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


    public function clickDayCount()
    {
        $culTimes = $this->container->getParameter('init');
        $date = date('Y-m-d');
        $request = $this->get('request');
        $uid = $request->getSession()->get('uid');
        $em = $this->getDoctrine()->getManager();
        $culTimes = $em->getRepository('JiliApiBundle:CheckinUserList')->countUserList($uid,$date);
        return $culTimes;

    }


    public function conUnion($str)
    {
        $pattern = '/[^\x00-\x80]/';
        if (preg_match($pattern, $str)) {
            return true; // "含有中文";
        } else {
            return false;
        }
    }

    public function isUnion($str)
    {
        if (!eregi("[^\x80-\xff]", "$str")) {
            return true; //全是中文
        } else {
            return false;
        }
    }

    public function my_substr($str, $start, $len)
    {
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

    public function countStrs($str)
    {
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

    public function getToken($email)
    {
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
    public function landingAction()
    {
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
        // pass the query param when redirect;
        $query  = array();
        if( $request->query->has('spm') ) {
            $query['spm'] =  $request->query->get('spm');
        }
        if ($token) {
            $request->getSession()->remove('token');
            $request->getSession()->set('token', $token);
        }
        $u_token = $request->getSession()->get('token');
        if (!$u_token) {

            $this->get('user_sign_up_route.listener')->refreshRouteSession( array('spm'=> $request->get('spm', null) ) );
            return $this->redirect($this->generateUrl('_user_reg' ));
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
                $this->get('user_sign_up_route.listener')->refreshRouteSession( array('spm'=> $request->get('spm', null) ) );
                return $this->redirect($this->generateUrl('_user_reg' ));
            }
        } else {
            $email = $wenuser[0]->getEmail();
        }

        $is_email = $em->getRepository('JiliApiBundle:User')->getWenwenUser($email);
        if ($is_email) {
            $is_user = $this->container->getParameter('init_one');
        } else {
            if ($request->getMethod() === 'GET') {
            } elseif ($request->getMethod() == 'POST') {
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

                    $this->get('user_sign_up_route.listener')->signed( array('user_id'=> $user->getId() ) );
                    return $this->redirect($this->generateUrl('_homepage'));
                }
            }
        }

        //最新动态
        $filename = $this->container->getParameter('file_path_recent_point');
        $recentPoint = FileUtil::readFileContent($filename);
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

    private function checkLanding($email, $nick, $pwd, $newPwd)
    {
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
    public function isExistVistAction()
    {
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
    public function infoVisitAction()
    {
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
                $code = $this->container->getParameter('init');
            }
        } else {
            $code = $this->container->getParameter('init');
        }
        return new Response($code);

    }

    /**
	 * @Route("/gameVisit", name="_default_gameVisit")
	 */
    public function gameVisitAction()
    {
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
    public function aboutAction()
    {
        return $this->render('JiliApiBundle:Default:about.html.twig');
    }

    /**
	 * @Route("/error", name="_default_error", requirements={"_scheme"="http"})
	 */
    public function errorAction()
    {
        return $this->render('JiliApiBundle::error.html.twig');
    }

    /**
	 * @Route("/services", name="_default_services", requirements={"_scheme"="http"})
	 */
    public function servicesAction()
    {
        return $this->render('JiliApiBundle::onservice.html.twig');
    }

    /**
	 * @Route("/support", name="_default_support", requirements={"_scheme"="http"})
	 */
    public function supportAction()
    {
        return $this->render('JiliApiBundle:Default:help.html.twig');
    }

    /**
	 * @Route("/service", name="_default_service", requirements={"_scheme"="http"})
	 */
    public function serviceAction()
    {
        return $this->render('JiliApiBundle:Default:service.html.twig');
    }

    /**
	 * @Route("/contact", name="_default_contact")
	 */
    public function contactAction()
    {
        $request = $this->get('request');
        $content = $request->query->get('content');
        $email = $request->query->get('email');
        $code = $this->checkContact($content, $email);
        return new Response($code);
    }

    public function checkContact($content, $email)
    {
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

    /**
	* @Route("/wenwenVisit", name="_default_wenwenVisit")
	*/
    public function wenwenVisitAction()
    {
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
    public function adLoginAction()
    {
        $request = $this->get('request');

        $session = $this->get('session');
        $code =$this->get('login.listener')->login($this->get('request'));
        $response = new Response($code);

        if ($request->request->has('remember_me')  &&  $request->request->get('remember_me') === '1') {

            if($session->has('uid')) {

                $request = $this->get('request');
                $email = $request->get('email');
                $pwd= $request->get('pwd');
                $token = $this->get('login.listener')->buildToken( array( 'email'=> $email, 'pwd'=> $pwd) );
                if( $token) {
                    $response->headers->setCookie(new Cookie("jili_rememberme", $token, time() + 3153600, '/'));
                } else {
                    // todo: set the error flash
                }

            }
        }
        return $response;
    }
}
